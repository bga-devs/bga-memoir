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
use M44\Helpers\Utils;
use M44\Managers\Terrains;
use M44\Models\Player;
use M44\Core\Game;

use function PHPSTORM_META\type;

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
    Globals::setInitHandDone(false);

    if (!Globals::isCampaign()) {
      Globals::setInitHandDone(true);
    }

   // TODO only once per round
    if (Globals::isCampaign()) {
      $team = Teams::get(Globals::getTeamTurn());

      // TO DO
      $this->gamestate->setAllPlayersMultiactive();
      $this->gamestate->nextState('reserveRoll');
    } else {
      $this->gamestate->nextState('prepareTurn');
    }
    // Check for options
    $options = Scenario::getOptions();
    if (isset($options['airdrop'])) {
      $team = Teams::get($options['airdrop']['side']);
      $this->changeActivePlayerAndJumpTo($team->getCommander(), ST_AIR_DROP);
      return;
    }
  }

  public function argsReserveUnits() 
  { 
    $args = [];
    $scenario = Globals::getScenario();
    $mode = Scenario::getMode();
    $sidePlayer1 = isset($scenario['game_info']['side_player1']) ? $scenario['game_info']['side_player1'] : 'AXIS';
    $dim = Board::$dimensions[$mode];
    $cells = Board::getListOfCells();
    
    // pass all players reserve args and active player from UI will select its own deployement args
    foreach(Teams::getAll() as $team) {
      $player = $team->getCommander();
      $yBackLine = $sidePlayer1 == $player->getTeam()->getId() ? 0 : $dim['y']-1;
       
      // filter cells on player backline and no unit on cells
      $cells_unit_deployement = array_filter($cells, function ($c) use ($yBackLine) {
        return $c['y'] == $yBackLine 
        && is_null(Board::getUnitInCell($c));
      });
      
      // select cells of player's units for sandbag deployement only on board not on reserve
      // and select cells from player units for advance 2 hexes action
      $player_units = $player->getTeam()->getUnits();
      $cells_sandbag_deployement = [];
      $cells_reachable_for_advance2 = [];
      foreach ($player_units as $unit) {
        if(!$unit->isOnReserveStaging()) {
          $cells_sandbag_deployement[$unit->getId()] = $unit->getPos();
          // get reachable cells at distance specific for reserve deployement
          $cells_reachable_for_advance2[$unit->getId()] = Board::getReachableCellsAtDistanceReserve($unit, 2);

        }
      }

      // max figures conditions
      $player_figures = [INFANTRY => 0, ARMOR => 0, ARTILLERY => 0];
      foreach ($player_units as $unit) {
        $type = $unit->getType();
        $figures = $unit->getNUnits();
        $player_figures [$type] = $player_figures[$type] + $figures;
      }

      $argsplayer = [
          'elements_to_deploy' => (object) Globals::getRollReserveList(),
          // add cells list at the player border to be selectable for unit deployement
          'cells_units_deployement' => $cells_unit_deployement,
          // add cells list for sandbags (cells on player's units)
          'cells_sandbag_deployement' => $cells_sandbag_deployement,
          // add cells list for wire (2 cells adjacent to units)
          // max units conditions
          'cells_advance2' => $cells_reachable_for_advance2,
          'n_units_by_type' => $player_figures,
          'max_units_by_type' => MAX_FIGURES_STANDARD,      
      ];
      $args[$player->getId()] = $argsplayer;
    }

    return $args;
  }

  public function actReserveUnitsDeployement($x = null, $y = null, $finished = false, $pId = null, 
    $elem = null, $isWild = false, $onStageArea = false, $unitId = null)
  {
    self::checkAction('actReserveUnitsDeployement');
    $args = $this->argsReserveUnits();


    if($finished)
    {
      // end of reserve deployement phase choosen by player (button action)
      $player = Players::get($pId);
      // remove latest selectable fields, latest button action, update title for this player
      Notifications::clearEndReserveDeployement($player);
      // wait for other player to finish reserve roll deployement
      $this->gamestate->setPlayerNonMultiactive($pId, 'done');
    } else { 
      // add unit from select ui cell
      $player = Players::get($pId);
      $scenario = Scenario::get();
      $country = $player->getTeam()->getCountry(); 
      $suffix = TROOP_NATION_MAPPING[$country];

      if ($onStageArea) {
        // case staging define a default 'dummy' position on the board
        $pos = $args[$pId]['cells_units_deployement'][array_key_first($args[$pId]['cells_units_deployement'])];
      } else {
        $pos = ['x' => $x, 'y' => $y];
      }
      

      switch ($elem) {
        case 'inf':
        case 'tank':
        case 'gun':
        case 'inf2':
        case 'tank2':
          // define unit name and badge from elem args
          $unit_type = ['name' => $elem . $suffix];
          // Case special forces or elite add a badge
          if (str_ends_with($elem, '2')) {
            $unit_type['badge'] = TROOP_BADGE_MAPPING[$country];
          }
          $unit = Units::addInCell($unit_type, $pos, $onStageArea);
          if (!$onStageArea) {
            Board::addUnit($unit);
          }          
          // decrease nb of reserve token
          $player->getTeam()->incNReserveTokens(-1);
          Notifications::ReserveUnitDeployement($player, $unit, $onStageArea);
        break;

        
        case 'sandbag':
          $unit = Board::getUnitInCell($x, $y);
          $sandbag = Terrains::add([
            'type' => 'sand',
            'tile' => 'sand',
            'x' => $x,
            'y' => $y,
            'orientation' => ($unit->getCampDirection() + 3) / 2,
          ]);
          Notifications::addTerrain(
            $player,
            $sandbag,
            \clienttranslate('${player_name} reinforces their position by placing a sandbag (in ${coordSource})')
          );
        break;

        case 'advance2':
          // move unit to targeted cell
          $player = Players::get($pId);
          $unit = Units::get($unitId);
          $startingCell = $unit-> getPos();
          $targetCell['x'] = $x;
          $targetCell['y'] = $y;
          Notifications::moveUnit($player, $unit, $startingCell, $targetCell);
          Board::moveUnit($unit, $targetCell);
        break;
        
        default:
          throw new \BgaVisibleSystemException('You cannot performed this kind of reinforcement here. Should not happen');
        break;
      }

      // remove element from list of remaining elements to be deployed
      $fullListToDeploy = Globals::getRollReserveList();
      $listToDeploy = $fullListToDeploy[$pId];
      if ($isWild) {
        $elem = in_array('wild',$listToDeploy) ? 'wild' : 'wild2';
      }
      $key = array_search($elem, $listToDeploy);
      unset($listToDeploy[$key]);
      //$listelem[] = $elem;
      //$listToDeployAfter = array_diff($listToDeploy, $listelem); 
      //$listToDeployAfter2= json_decode(json_encode($listToDeployAfter), true);
      $fullListToDeploy[$pId] = $listToDeploy;
      //var_dump('reste a deployer', $listToDeploy, $listelem, $listToDeployAfter, $fullListToDeploy);
      Globals::setRollReserveList($fullListToDeploy);

      // deployement may continue if there are remaining reserve tokens
      // and if there are still unit or elements to be deployed
      if ($player->getTeam()->getNReserveTokens() > 0 
        && !empty($listToDeploy)) {
        // loop back in Reserve Roll Deployement state until all players finished 
        // or nothing to deploy 
        // or no other tokens
        $this->gamestate->nextPrivateState($pId, 'again');
      } else {

        // thus no other possible action
        // remove latest selectable fields, latest button action, update title for this player
        Notifications::clearEndReserveDeployement($player);
        // wait for other player to finish reserve roll deployement
        $this->gamestate->setPlayerNonMultiactive($pId, 'done');

      }
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
    }
    $this->gamestate->setAllPlayersMultiactive();
    //this is needed when starting private parallel states
    //players will be transitioned to initialprivate state defined in master state
    $this->gamestate->initializePrivateStateForAllActivePlayers(); 
    // note : next private state is ST_RESERVE_ROLL_DEPLOYEMENT
  }

  public function stReserveRollDeployement()
  {
    $args = $this->argsReserveUnits();
  }



  public static function ReserveRoll($player) 
  {
    $results = Dice::roll($player, 2, null, false);
    $reserveElements = [];

    // upon results determine list of units, obstacles (sand bags, wires, airpower tokens,..) to be deployed
    $reserveRollMap = [
      \DICE_INFANTRY => 'inf',
      \DICE_ARMOR => 'tank',
      \DICE_GRENADE => 'wild',
      \DICE_FLAG => 'sandbag'];

    // get campaign special rolls elements/actions to deploy
    $scenarioId = Globals::getScenario()['meta_data']['scenario_id'];
    $teamId = $player->getTeam()->getId();
    $reserveRollSpecial = Globals::getCampaign()['scenarios'][$teamId]['reserve_roll_special'][$scenarioId];

    // upon roll results, create a list of possible combinations
    if (in_array(\DICE_STAR, $results)) {
      if ($results == [\DICE_STAR, \DICE_STAR]) {
        if (!is_null($reserveRollSpecial['star_star'])) {
          $reserveElements[] = $reserveRollSpecial['star_star'];          
        }
        
      } elseif ($results == [\DICE_STAR, \DICE_FLAG] || $results == [\DICE_FLAG, \DICE_STAR]) {
        if (!is_null($reserveRollSpecial['flag_star'])) {
          $reserveElements[] = $reserveRollSpecial['flag_star'];          
        }
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
