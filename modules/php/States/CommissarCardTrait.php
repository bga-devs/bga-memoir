<?php
namespace M44\States;

use M44\Core\Notifications;
use M44\Helpers\Utils;
use M44\Managers\Cards;
use M44\Managers\Players;
use M44\Managers\Teams;

trait CommissarCardTrait
{
  function isInitialCommissar()
  {
    return Cards::getInLocation(['commissar', '%'])->empty();
  }

  function argsCommissarCard($privatise = true)
  {
    $player = Players::getActive();
    $cards = $player->getCards()->filter(function ($card) {
      return $card->getType() != \CARD_AMBUSH;
    });

    $playableCards = [];
    if (!$this->isInitialCommissar()) {
      $playableCards = $player
        ->getCards()
        ->filter(function ($card) {
          return in_array($card->getType(), [\CARD_RECON, \CARD_COUNTER_ATTACK]);
        })
        ->map(function ($card) use ($player) {
          return $player->canHill317() && $card->canHill317();
        });
    }

    $args = [
      'cards' => $cards,
      'playableCards' => $playableCards,
    ];
    return $privatise ? Utils::privatise($args) : $args;
  }

  function actCommissarCard($cardId)
  {
    // Sanity check
    $this->checkAction('actCommissarCard');
    $player = Players::getCurrent();
    $args = $this->argsCommissarCard(false);
    $isInitial = $this->isInitialCommissar();

    if (!in_array($cardId, $args['cards']->getIds())) {
      throw new \BgaVisibleSystemException('Non playable card. Should not happen.');
    }

    // Move the card under the token to inplay location, if any
    if (!$isInitial) {
      $card = Cards::revealCommissar($player);
      Notifications::revealCommissarCard($player, $card);
    }

    // Put the card under the token
    $card = Cards::commissar($player, $cardId);
    Notifications::commissarCard($player, $card);

    if ($isInitial) {
      $team = Teams::getTeamTurn();
      $player = $team->getMembers()->first();
      $this->changeActivePlayerAndJumpTo($player, \ST_PLAY_CARD);
    } else {
      $this->gamestate->nextState('play');
    }
  }

  function stPlayCommissarCard()
  {
    $args = self::argsPlayCommissarCard();
    if ($args['sections'] == null && !$args['canHill317']) {
      $this->actPlayCommissarCard(null, false, false);
    }
  }

  function argsPlayCommissarCard()
  {
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    return [
      'cardId' => $card->getId(),
      'sections' => $card->getAdditionalPlayConstraints(),
      'canHill317' => $player->canHill317() && $card->canHill317(),
    ];
  }

  function actPlayCommissarCard($sectionId = null, $hill317 = false, $check = true)
  {
    // Sanity check
    if ($check) {
      $this->checkAction('actPlayCommissarCard');
    }
    $player = Players::getCurrent();
    $args = $this->argsPlayCommissarCard();
    $card = $player->getCardInPlay();

    if (!is_null($args['sections']) && (!in_array($sectionId, $args['sections']) || is_null($sectionId))) {
      throw new \BgaVisibleSystemException('Invalid section. Should not happen');
    }

    if (is_null($args['sections']) && !is_null($sectionId)) {
      throw new \BgaVisibleSystemException('Invalid section. Should not happen');
    }

    if ($hill317 && !$player->canHill317()) {
      throw new \BgaVisibleSystemException('Cannot play card as hill317. Should not happen');
    }

    if ($hill317 && !$card->canHill317()) {
      throw new \BgaVisibleSystemException('Cannot play this type of card as hill317. Should not happen');
    }

    if ($hill317) {
      $card->setExtraDatas('hill317', true);
    }
    $card->setExtraDatas('section', $sectionId);
    $this->gamestate->nextState($card->nextStateAfterPlay());
  }
}
