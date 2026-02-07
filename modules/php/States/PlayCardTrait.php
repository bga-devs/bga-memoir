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
      $card = Cards::getInstance(CARD_AIR_POWER);
      $card->setPlayer($player->getId());
      $cardId = Cards::getIdByType(CARD_AIR_POWER);
      $card->setId($cardId);      
      
      $nextState = $card->nextStateAfterPlay();
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

  function argsDistributeCards($player = NULL) 
  { // Overlord distribution cards args
    $singleActive = is_null($player);
    $player = $player ?? Players::getActive();
    $teamId = $player ->getTeam()-> getId();
    $cards = $player
      ->getCards()
      ->filter(function ($card) {
        return $card->getType() != \CARD_AMBUSH;
      })
      ->map(function ($card) use ($teamId) {
        // get subsection restriction (if applicable)
        return $card->getPlayableSubSections($teamId);
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

    // give list of already distributed cards to avoid double selection 
    $distributedCards = Globals::getDistributedCards();
    $teamId = $player ->getTeam()-> getId();
    if (isset($distributedCards[$teamId])) {
      foreach ($distributedCards[$teamId] as $distributedCard) {
        $cardIdDistributed = $distributedCard['cardId'];
        unset($cards[$cardIdDistributed]);
      }
    }

    // give list of selected subSections to avoid double selection
    $selectedSubSections = [];
    if (isset($distributedCards[$teamId])) {
      foreach ($distributedCards[$teamId] as $distributedCard) {
        $selectedSubSections[] = $distributedCard['subSection'];
      }
    }


    //only in Campaign Mode (not probable to have Campaign mode in overlord so far)
    //$hasAirPowerTokens = $player->getTeam()->hasAirPowerTokens();
    //var_dump('argsDistributeCards called', $selectedSubSections);

    $args = [
      'cards' => $cards,
      'cardsHill317' => $cardsHill317,
      'cardsBlowBridge' => $cardBlowBridge,
      'cardsArmorBreakthrough' => $cardArmorBreakthrough,
      'actionCount' => Globals::getActionCount(),
      'selectedSubSections' => $selectedSubSections,
    ];
    $args = $singleActive ? Utils::privatise($args) : $args;
    return $args;
  }

  function stDistributeCards()
  { // Overlord distribution cards state
    // Commander preparation Choose 1 to 3 cards to be played on mains sections and get list of those cards
    
    // TO DO specific Comissar in Est Theater extensions
    /*if (Globals::getCommissar() != '' && $this->isInitialCommissar()) {
      $pId = Teams::get(Globals::getCommissar())->getCommander();
      $this->changeActivePlayerAndJumpTo($pId, \ST_COMMISSAR);
    }*/
    $commander = Players::getActive()->getTeam()->getCommander();
    $this->argsDistributeCards($commander);
    

  }

  function actDistributeCards($cardId, $sectionId)
  { // 1 to 3 cards choosen from UI to be played on mains sections and get list of those cards
    // prepare list of those cards and place them on corresponding main and secondary sections
    // to be added on Globals card List to be played later
    // var_dump('choosen card', $cardId, $sectionId);

    // Store the cardId and its selected subSection in Globals database
    $list = Globals::getDistributedCards();
    $commander = Players::getActive()->getTeam()->getCommander();
    $teamId = $commander ->getTeam()-> getId();
    $list[$teamId][] = ['cardId' => $cardId, 'subSection' => $sectionId];
    Globals::setDistributedCards($list);
    // push card in inplay
    $card = Cards::play($commander, $cardId, $sectionId);
    // update UI in subSection
    Notifications::distributeCard($commander, $card, $sectionId);

    // Check if others cards can be still distributed
    // Condition max number, condition 1 car maxi per sub section and max 1 commander chief
    if (count($list[$teamId]) >= 3) {
      // max 3 cards distributed
      //$this->nextState('nextPlayer'); // Next state after distribution to be confirmed
    } else {
      $args = $this->argsDistributeCards($commander);
      $this->nextState('distributeCards'); // stay in distribution state
    }
  }
}
