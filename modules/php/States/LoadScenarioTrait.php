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
        $this->gamestate->setAllPlayersMultiactive();
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

  public function actGetScenarios($query = [])
  {
    if (!isset($query['type'])) {
      $map = [
        \OPTION_MODE_STANDARD => 'STANDARD',
        \OPTION_MODE_BREAKTHROUGH => 'BRKTHRU',
        \OPTION_MODE_OVERLORD => 'OVERLORD',
      ];
      $query['type'] = $map[Globals::getMode()];
    }

    return Scenario::getPaginatedScenarios($query);
  }

  public function actGetScenarioInfo($id)
  {
    return Scenario::getFromTheFront($id);
  }

  public function argsProposeScenario()
  {
    $id = Globals::getScenarioId();
    return [
      'canPropose' => Globals::getActionCount() <= 2,
      'result' => $this->actGetScenarios(),
      'scenarioProposed' => $id == -1 ? null : $this->actGetScenarioInfo($id),
    ];
  }

  public function actProposeScenario($id)
  {
    self::checkAction('actProposeScenario');
    $player = Players::getCurrent();
    if (Globals::getActionCount() == 2) {
      throw new \BgaVisibleSystemException('Cannot counter counter a choice of scenario');
    }
    $previousId = Globals::getScenarioId();
    $id = (int) $id;
    $scenario = Scenario::getFromTheFront($id);
    Globals::incActionCount();
    Globals::setScenarioId($id);
    Globals::setLobbyProposalPId($player->getId());
    Globals::setOfficialScenario(false);

    Notifications::proposeScenario($player, $scenario, Globals::getActionCount() == 2);
    $this->gamestate->nextState('next');
  }

  public function stLobbyNextPlayer()
  {
    $pId = Globals::getLobbyProposalPId();
    $this->gamestate->changeActivePlayer(Players::getNextId($pId));
    $this->gamestate->nextState(Globals::getActionCount() == 1 ? 'second' : 'final');
  }

  public function actValidateScenario($accept)
  {
    self::checkAction('actValidateScenario');
    if ($accept) {
      Notifications::message(clienttranslate('Players agree on a senario. Setup in progress'));
      Scenario::validateScenario(Scenario::getFromTheFront(Globals::getScenarioId()));
      Globals::setScenario(Scenario::getFromTheFront(Globals::getScenarioId()));
      Globals::setRound(0);
      Globals::setActionCount(0);
      $this->stNewRound(true);
    } else {
      Notifications::message(clienttranslate('Players do not agree on the scenario. Ending the game'), []);
      self::DbQuery("UPDATE player SET player_score='0', player_score_aux='-4242'");
      self::reloadPlayersBasicInfos();
      $this->gamestate->nextState('reject');
    }
  }
}
