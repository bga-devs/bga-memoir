<?php

namespace M44\States;

use M44\Core\Notifications;
use M44\Core\Globals;
use M44\Helpers\Utils;
use M44\Managers\Cards;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Models\Player;

trait PlayCardTrait
{
  function stPlayCard()
  {
    if (Globals::getCommissar() != '' && $this->isInitialCommissar()) {
      $pId = Teams::get(Globals::getCommissar())->getCommander();
      $this->changeActivePlayerAndJumpTo($pId, \ST_COMMISSAR);
    }
  }

  function argsPlayCard($player = null)
  {
    $singleActive = is_null($player);
    $player = $player ?? Players::getActive();
    $cards = $player
      ->getCards()
      ->filter(function ($card) {
        return $card->getType() != \CARD_AMBUSH;
      })
      ->map(function ($card) {
        // get section restriction (if applicable)
        return $card->getAdditionalPlayConstraints();
      });

    $cardsHill317 = [];
    if ($player->canHill317()) {
      $cardsHill317 = $player
        ->getCards()
        ->filter(function ($card) {
          return $card->canHill317();
        })
        ->getIds();
    }

    $cardBlowBridge = [];
    if ($player->canBlowBridge()) {
      $cardBlowBridge = $player
        ->getCards()
        ->filter(function ($card) {
          return $card->canBlowBridge();
        })
        ->getIds();
    }

    $cardArmorBreakthrough = [];
    if ($player->canArmorBreakthrough()) {
      $cardArmorBreakthrough = $player
        ->getCards()
        ->filter(function ($card) {
          return $card->canArmorBreakthrough();
        })
        ->getIds();
    }


    $hasAirPowerTokens = $player->getTeam()->hasAirPowerTokens();

    $args = [
      'cards' => $cards,
      'cardsHill317' => $cardsHill317,
      'cardsBlowBridge' => $cardBlowBridge,
      'cardsArmorBreakthrough' => $cardArmorBreakthrough,
      'actionCount' => Globals::getActionCount(),
    ];
    $args = $singleActive ? Utils::privatise($args) : $args;
    $args['hasAirpowerToken'] = $hasAirPowerTokens;
    return $args;
  }

  function actPlayCard($cardId, $sectionId = null, $hill317 = false, $canBlowbridge = false, $airPowerTokenUsed = false, $armorBreakthrough = false)
  {
    // Sanity check
    $this->checkAction('actPlayCard');
    Globals::incActionCount();
    $player = Players::getCurrent();
    $args = $this->argsPlayCard($player);
   
    if ($airPowerTokenUsed) {
      // create a flag AirPowerToken Used to future conditions as no card used
      Globals::setAirPowerTokenUsed(true);
      // remove AirPower Token
      $airPowerTokens = Globals::getAirPowerTokens();
      $teamId = $player->getTeam()->getId();
      unset($airPowerTokens[array_search($teamId, $airPowerTokens)]);
      Globals::setAirPowerTokens($airPowerTokens);
      // notify
      Notifications::removeAirpowerToken($player);      
      
      $nextState = 'airpower';
    } else {

      if (!in_array($cardId, $args['cards']->getIds())) {
        throw new \BgaVisibleSystemException('Non playable card. Should not happen.');
      }
  
      if (!is_null($args['cards'][$cardId]) && (!in_array($sectionId, $args['cards'][$cardId]) || is_null($sectionId))) {
        throw new \BgaVisibleSystemException('Invalid section. Should not happen');
      }
  
      if (is_null($args['cards'][$cardId]) && !is_null($sectionId)) {
        throw new \BgaVisibleSystemException('Invalid section. Should not happen');
      }
  
      if ($hill317 && !$player->canHill317()) {
        throw new \BgaVisibleSystemException('Cannot play card as hill317. Should not happen');
      }
  
      if ($hill317 && !Cards::get($cardId)->canHill317()) {
        throw new \BgaVisibleSystemException('Cannot play this type of card as hill317. Should not happen');
      }

      if ($armorBreakthrough && !$player->canArmorBreakthrough()) {
        throw new \BgaVisibleSystemException('Cannot play this type of card as Armor Breakthrough. Should not happen');
      }
  
      if ($hill317) {
        $card = Cards::get($cardId);
        $card->setExtraDatas('hill317', true);
      }
  
      if ($canBlowbridge) {
        $card = Cards::get($cardId);
        $card->setExtraDatas('canblowbridge', true);
      }

      if ($armorBreakthrough) {
        $card = Cards::get($cardId);
        $card->setExtraDatas('canArmorBreakthrough', true);
      }
  
      // Play the card
      $card = Cards::play($player, $cardId, $sectionId);
      $card->onPlay();
      Notifications::playCard($player, $card);
      $nextState = $card->nextStateAfterPlay();
  
      // Handle first turn of Russian
      if ($player->isCommissar() && $player->getCommissarCard() == null) {
        $nextState = 'commissar';
      }
    }

    $this->nextState($nextState);
  }
}
