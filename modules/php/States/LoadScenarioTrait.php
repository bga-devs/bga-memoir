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
      if (Globals::getScenarioSource() == OPTION_SCENARIO_SOURCE_DOW) {
        $this->gamestate->setAllPlayersMultiactive();
        $this->gamestate->nextState('lobby');
      } else {
        $this->activeNextPlayer();
        $this->gamestate->nextState('upload');
      }
    }
  }

  //////////////////////////////////////////
  //  _   _       _                 _
  // | | | |_ __ | | ___   __ _  __| |
  // | | | | '_ \| |/ _ \ / _` |/ _` |
  // | |_| | |_) | | (_) | (_| | (_| |
  //  \___/| .__/|_|\___/ \__,_|\__,_|
  //       |_|
  //////////////////////////////////////////
  public function actUploadScenario($scenario)
  {
    Globals::setRound(0);
    Globals::setScenario($scenario);
    $this->stNewRound(true);
  }

  ////////////////////////////////////
  //  _          _     _
  // | |    ___ | |__ | |__  _   _
  // | |   / _ \| '_ \| '_ \| | | |
  // | |__| (_) | |_) | |_) | |_| |
  // |_____\___/|_.__/|_.__/ \__, |
  //                         |___/
  ////////////////////////////////////

  public function actGetScenarios($filters)
  {
    $type = Globals::getMode();
    if ($type == \OPTION_MODE_STANDARD) {
      $type = 'STANDARD';
    } elseif ($type == \OPTION_MODE_BREAKTHROUGH) {
      $type = 'BRKTHRU';
    } else {
      $type = 'OVERLORD';
    }
    // filters = ['front', 'name', 'id', 'author' ]

    return Scenario::getPaginatedScenarios($filters['page'] ?? 1, $type, $filters, $filters['pagination']);
  }

  public function actGetScenarioInfo($id)
  {
    return Scenario::getFromTheFront($id);
  }

  public function argsProposeScenario()
  {
    return ['scenario' => Scenario::getFromTheFront(Globals::getScenarioId())];
  }

  public function actProposeScenario($id)
  {
    self::checkAction('actProposeScenario');

    if (Globals::getActionCount() == 1) {
      throw new \BgaVisibleSystemException('Cannot counter counter a choice of scenario');
    }
    $previousId = Globals::getScenarioId();
    $id = (int) $id;
    $scenario = Scenario::getFromTheFront($id);
    if ($previousId !== null) {
      Globals::setActionCount(1);
    }
    Globals::setScenarioId($id);
    Globals::setOfficialScenario(false);

    Notifications::proposeScenario(Players::getActive(), $scenario, $previousId != null);
    $this->gamestate->nextState('counter');
  }

  public function actValidateScenario($valid)
  {
    self::checkAction('actValidateScenario');
    if ($valid === false) {
      Notifications::message(clienttranslate('Players do not agree on the scenario. Ending the game'), []);
      $this->gamestate->nextState('reject');
    } else {
      Notifications::message(clienttranslate('Players agree on a senario. Setup in progress'));
      Scenario::validateScenario(Scenario::getFromTheFront(Globals::getScenarioId()));
      Globals::setScenario(Scenario::getFromTheFront(Globals::getScenarioId()));
      Globals::setRound(0);
      Globals::setActionCount(0);
      $this->stNewRound(true);
    }
  }

  public function stProposeChange()
  {
    $this->activeNextPlayer();
    $this->gamestate->nextState('');
  }
}
