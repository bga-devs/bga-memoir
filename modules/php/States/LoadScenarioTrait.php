<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Cards;
use M44\Managers\Units;
use M44\Helpers\Utils;
use M44\Board;
use M44\Scenario;

trait LoadScenarioTrait
{
  public function stStartTable()
  {
    if (Globals::isOfficialScenario()) {
      $scenarioId = Globals::getScenarioId();
      Scenario::loadId($scenarioId);
      $this->gamestate->jumpToState(\ST_NEW_ROUND);
    } else {
      $this->gamestate->setAllPlayersMultiactive();
      $this->gamestate->nextState('upload');
    }
  }

  public function actUploadScenario($scenario)
  {
    Globals::setRound(0);
    Globals::setScenario($scenario);
    $this->stNewRound(true);
  }
}
