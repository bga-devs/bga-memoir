<?php

namespace M44\States;

use M44\Board;
use M44\Core\Globals;
use M44\Core\Stats;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Cards;
use M44\Managers\Units;
use M44\Core\Notifications;
use M44\Dice;
use M44\Scenario;

trait RoundTrait
{
  public function stNewRound($forceRefresh = false)
  {
    $round = Globals::incRound();
    $rematch = $round == 2;
    Scenario::setup($rematch, $forceRefresh);
    Globals::setUnitMoved(-1);
    Globals::setUnitAttacker(-1);
    Globals::setLastPlayedCards([]);
    Globals::setRawLastPlayedCards([]);
    Globals::setAttackStack([]);
    Globals::setAirDrop2Done(false);
    Globals::setSupplyTrainDone(false);
    Globals::setRollReserveDone(false);

   // TODO only once per round
    if (Globals::isCampaign()) {
      $team = Teams::get(Globals::getTeamTurn());
      $this->changeActivePlayerAndJumpTo($team->getCommander(), ST_RESERVE_ROLL_DEPLOYEMENT);
      return;
    }
    // Check for options
    $options = Scenario::getOptions();
    if (isset($options['airdrop'])) {
      $team = Teams::get($options['airdrop']['side']);
      $this->changeActivePlayerAndJumpTo($team->getCommander(), ST_AIR_DROP);
      return;
    }

    $this->gamestate->jumpToState(\ST_PREPARE_TURN);
  }

  public function argsReserveUnits() 
  { 
    $player = Players::getActive();
    $scenario = Globals::getScenario();
    $mode = Scenario::getMode();
    $sidePlayer1 = isset($scenario['game_info']['side_player1']) ? $scenario['game_info']['side_player1'] : 'AXIS';
    $dim = Board::$dimensions[$mode];
    $yBackLine = $sidePlayer1 == $player->getTeam()->getId() ? 0 : $dim['y']-1;
    $cells = Board::getListOfCells();
    $cells_unit_deployement = array_filter($cells, function ($c) use ($yBackLine) {
      return $c['y'] == $yBackLine 
      && is_null(Board::getUnitInCell($c));
    });  // filter cells on player backline and no unit on cells
    return [
      'playerid' => $player->getId(),
      'elements_to_deploy' => Globals::getRollReserveList(),
      // add cells list at the player border to be selectable for unit deployement
      'cells_units_deployement' => $cells_unit_deployement,
      // add cells list for sandbags (cells on player's units)
      // add cells list for wire (2 cells adjacent to units)

    ];
  }

  public function actReserveUnitsDeployement($x = null, $y = null, $finished = false)
  {
    self::checkAction('actReserveUnitsDeployement');
    
    $args = self::argsReserveUnits();

    
    // Passer les arguments a l'Ui
    //$args = self::argsReserveUnits();
    

    if($finished)
    {
      $this->gamestate->jumpToState(\ST_PREPARE_TURN);
    } else { 
      $this->gamestate->jumpToState(\ST_RESERVE_ROLL_DEPLOYEMENT);
    }
  }

  public function stReserveRoll()
  {
    if (!Globals::getRollReserveDone()) {
      $list = [];
      foreach(Teams::getAll() as $team) {
        $player = $team->getCommander();
        // ajouter une condition si pas de jeton de reserve disponible
        $elementsToDeploy = self::ReserveRoll($player);
        $list[$player->getId()] = $elementsToDeploy;
        //$list[] = [$player->getId() => $elementsToDeploy];        
      }
      Globals::setRollReserveList($list);
      Globals::setRollReserveDone(true);
      $args = self::argsReserveUnits();
    }
  }

  public function ReserveRoll($player) 
  {
    $results = Dice::roll($player, 2, null, false);
    $reserveElements = [];

    // upon results determine list of units, obstacles (sand bags, wires, airpower tokens,..) to be deployed
    $reserveRollMap = [
      \DICE_INFANTRY => 'inf',
      \DICE_ARMOR => 'tank',
      \DICE_GRENADE => 'wild',
      \DICE_FLAG => 'sandbag'];

    // upon roll results, create a list of possible combinations
    if (in_array(\DICE_STAR, $results)) {
      if ($results == [\DICE_STAR, \DICE_STAR]) {
        $reserveElements[] = 'airpowertoken';
      } else {
        $results2 = array_diff($results,[\DICE_STAR]);
        $reserveElements[] = $reserveRollMap[$results2[array_key_first($results2)]].'2';
      }
    } else {
      foreach ($results as $d) {
        $reserveElements[] = $reserveRollMap[$d];
      }
    }
    return $reserveElements;
    
        
    // when done, Cards are distributed
    //Cards::initHands();


    //$this->gamestate->jumpToState(\ST_PREPARE_TURN);
  }

  public function stEndOfRound()
  {
    $round = Globals::getRound();
    $maxRound = Globals::isTwoWaysGame() ? 2 : 1;
    if ($round == $maxRound) {
      $this->gamestate->jumpToState(\ST_END_OF_GAME);
    } else {
      $this->gamestate->setAllPlayersMultiactive();
      $this->gamestate->nextState('change');
    }
  }

  public function argsChangeOfRound()
  {
    $teamNames = [
      ALLIES => \clienttranslate('Allies'),
      AXIS => \clienttranslate('Axis'),
    ];
    $team = Teams::getWinner();
    return [
      'i18n' => ['team'],
      'team' => $teamNames[$team->getId()],
    ];
  }

  public function actProceed()
  {
    self::checkAction('actProceed');
    $pId = $this->getCurrentPId();
    $this->gamestate->setPlayerNonMultiactive($pId, 'done');
  }

  public function stEndOfGame()
  {
    $nRounds = Globals::isTwoWaysGame() ? 2 : 1;

    foreach (Players::getAll() as $player) {
      $nWins = 0;
      $nFigs = 0;
      for ($i = 1; $i <= $nRounds; $i++) {
        $nWins += $player->getStat('medalRound' . $i);
        foreach (['inf', 'armor', 'artillery'] as $type) {
          $nFigs += $player->getStat($type . 'FigRound' . $i);
        }
      }

      $player->setScore($nWins);
      $player->setScoreAux($nFigs);
    }

    $this->gamestate->nextState('');
  }
}
