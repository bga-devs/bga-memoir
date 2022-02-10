<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Units;
use M44\Managers\Cards;
use M44\Helpers\Utils;
use M44\Board;

trait DrawCardsTrait
{
  public function stDrawCard()
  {
    $choice = false;
    $player = $player ?? Players::getActive();
    $card = $player->getCardInPlay();
    $method = $card->getDrawMethod();
    if ($method['keep'] != $method['draw']) {
      $choice = true;
    }

    if (!$choice) {
      $cards = Cards::pickForLocation($method['draw'], 'deck', ['hand', $player->getId()]);
    } else {
      $cards = Cards::pickForLocation($method['draw'], 'deck', ['choice', $player->getId()]);
    }
    Notifications::drawCards($player, $cards);
    // transition to choice if more than 1
    if (!$choice) {
      $this->gamestate->nextState('endRound');
    } else {
      $this->gamestate->nextState('choice');
    }
  }

  public function argsDrawChoice()
  {
    return [];
  }

  public function actChooseCard($cardId)
  {
    // keep this card, remove the others
  }
}
