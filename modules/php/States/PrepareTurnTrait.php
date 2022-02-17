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
  function stEndRound()
  {
    // Change team
    $currentSide = Globals::getSideTurn();
    $newSide = $currentSide == ALLIES ? AXIS : ALLIES;
    Globals::setSideTurn($newSide);

    // Update all tables with temp data
    Units::reset();
    Globals::setUnitMoved(-1);
    $this->gamestate->nextState('next');
  }


  function stPrepareTurn()
  {
    Globals::incTurn();

    // TODO : Overlord => branch here to distribute cards instead
    if (true) {
      $player = Players::getSide();
      Players::changeActive($player);
      Globals::setActivePlayer($player->getId());
      $this->gamestate->nextState('playCard');
    } else {
      // Activate commander in chief only
      // TODO
      $this->gamestate->nextState('distributeCard');
    }
  }

  function stChangePlayer()
  {
    $currentPlayer = Players::getActive()->getId();
    $nextPlayer = Players::getNextId($currentPlayer);
    $activePlayer = Globals::getActivePlayer();

    // TODO: manage overlord
    if ($activePlayer == $currentPlayer) {
      $this->gamestate->changeActivePlayer($nextPlayer);
    } else {
      $this->gamestate->changeActivePlayer($activePlayer);
    }
    $this->gamestate->nextState('next');
  }
}
