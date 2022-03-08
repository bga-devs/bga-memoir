<?php
namespace M44\States;

use M44\Core\Notifications;
use M44\Helpers\Utils;
use M44\Managers\Cards;
use M44\Managers\Players;

trait PlayCardTrait
{
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

    $args = [
      'cards' => $cards,
    ];
    return $singleActive ? Utils::privatise($args) : $args;
  }

  function actPlayCard($cardId, $sectionId = null)
  {
    // Sanity check
    $this->checkAction('actPlayCard');
    $player = Players::getCurrent();
    $args = $this->argsPlayCard($player);

    if (!in_array($cardId, $args['cards']->getIds())) {
      throw new \BgaVisibleSystemException('Non playable card. Should not happen.');
    }

    if (
      $args['cards'][$cardId] != null &&
      (!in_array($sectionId, $args['cards'][$cardId]) || $sectionId == null)
    ) {
      throw new \BgaVisibleSystemException('Invalid section. Should not happen');
    }

    if ($args['cards'][$cardId] == null && $sectionId != null) {
      throw new \BgaVisibleSystemException('Invalid section. Should not happen');
    }

    // Play the card
    $card = Cards::play($player, $cardId, $sectionId);
    Notifications::playCard($player, $card);
    $nextState = $card->nextStateAfterPlay();
    $this->gamestate->nextState($nextState);
  }
}
