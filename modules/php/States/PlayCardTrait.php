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
    $cardIds = $player
      ->getCards()
      ->filter(function ($card) {
        return true; // TODO : remove ambush and I think that's all ?
      })
      ->getIds();

    $args = [
      'cardIds' => $cardIds,
    ];
    return $singleActive ? Utils::privatise($args) : $args;
  }

  function actPlayCard($cardId)
  {
    // Sanity check
    $this->checkAction('actPlayCard');
    $player = Players::getCurrent();
    $args = $this->argsPlayCard($player);
    if (!in_array($cardId, $args['cardIds'])) {
      throw new BgaVisibleSystemException('Non playable card. Should not happen.');
    }

    // Play the card
    $card = Cards::play($player, $cardId);
    Notifications::playCard($player, $card);
    $nextState = 'selectUnits'; // TODO : $card->getNextState(...);
    $this->gamestate->nextState($nextState);
  }
}