<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Cards;
use M44\Managers\Units;
use M44\Core\Notifications;

trait PrepareTurnTrait
{
  function stPrepareTurn()
  {
    Globals::incTurn();

    // TODO : Overlord => branch here to distribute cards instead
    if (true) {
      $player = Players::getSide();
      Players::changeActive($player);
      $this->gamestate->nextState('playCard');
    } else {
      // Activate commander in chief only
      $this->gamestate->nextState('distributeCard');
    }
  }

  function stEndRound()
  {
    // discard card
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    $card = Cards::move($card->getId(), 'discard');
    Notifications::discard($player, [$card]);
    // change team
    $nextPlayer = Players::getNextId($player);
    Globals::setSideTurn(Players::get($nextPlayer)->getTeam()['side']);

    // update all tables with temp data
    Units::reset();
    Globals::setUnitMoved(-1);
    $this->gamestate->nextState('next');
  }
}
