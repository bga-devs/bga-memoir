<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Managers\Players;
use M44\Managers\Teams;

trait PrepareTurnTrait
{
  function stPrepareTurn()
  {
    Globals::incTurn();

    // TODO : Overlord => branch here to distribute cards instead
    if(true){
      $player = Players::getSide();
      Players::changeActive($player);
      $this->gamestate->nextState('playCard');
    } else {
      // Activate commander in chief only
      $this->gamestate->nextState('distributeCard');
    }
  }
}
