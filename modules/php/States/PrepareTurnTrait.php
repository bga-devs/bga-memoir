<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Cards;
use M44\Managers\Units;
use M44\Core\Notifications;
use M44\Scenario;

trait PrepareTurnTrait
{
  function stEndRound()
  {
    // PEGASUS
    if(Scenario::getId() == 2 && Globals::getTurn() <= 4 && Globals::getTeamTurn() == AXIS){
      // TODO : better handling of drawing for teams
      $player = Players::getActive();
      $cards = Cards::pickForLocation(1, 'deck', ['hand', $player->getId()]);
      Notifications::drawCards($player, $cards);
    }

    // Change team
    Teams::changeTeamTurn();

    // Update all tables with temp data
    Units::reset();
    Globals::setUnitMoved(-1);
    Globals::setUnitAttacker(-1);
    Notifications::clearUnitsStatus();
    $this->nextState('next');
  }

  function stPrepareTurn()
  {
    Globals::incTurn();

    // TODO : Overlord => branch here to distribute cards instead
    if (true) {
      $team = Teams::getTeamTurn();
      $player = $team->getMembers()->first();
      $this->nextState('playCard', $player->getId());
    } else {
      // Activate commander in chief only
      // TODO
      $this->gamestate->nextState('distributeCard');
    }
  }
}
