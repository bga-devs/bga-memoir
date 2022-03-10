<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Cards;
use M44\Managers\Units;
use M44\Core\Notifications;
use M44\Scenario;

trait RoundTrait
{
  public function stNewRound()
  {
    $round = Globals::incRound();
    $maxRound = Globals::isTwoWaysGame() ? 2 : 1;
    if ($round > $maxRound) {
      die('todo : launch EOG');
      // TODO : end of game
    }

    $rematch = $round == 2;
    Scenario::setup($rematch);
    Globals::setUnitMoved(-1);
    Globals::setUnitAttacker(-1);
    Globals::setLastPlayedCards([]);
    Globals::setAttackStack([]);
    $this->gamestate->jumpToState(\ST_PREPARE_TURN);
  }
}
