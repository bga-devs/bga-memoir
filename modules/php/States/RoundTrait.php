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
      Globals::setAirPowerTokens(null);
      Globals::setAirPowerTokenUsed(false);
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
       
      // filter cells on player backline and no unit on cells nor impassable terrains
      $units = $team->getUnits()->toArray();
      $unit = $units[0];
      
      $cells_unit_deployement = array_filter($cells, function ($c) use ($yBackLine, $unit) {
        return $c['y'] == $yBackLine 
        && is_null(Board::getUnitInCell($c))
        && !Board::isImpassableCell($c, $unit);
      });
      
      // select cells of player's units for sandbag deployement only on board not on reserve
      // and select cells from player units for advance 2 hexes action
      $player_units = $player->getTeam()->getUnits();
      $cells_sandbag_deployement = [];
      $cells_reachable_for_advance2 = [];
      $cells_wire_deployement = [];
      foreach ($player_units as $unit) {
        if(!$unit->isOnReserveStaging()) {
          $cells_sandbag_deployement[$unit->getId()] = $unit->getPos();
          // get reachable cells at distance specific for reserve deployement
          $cells_reachable_for_advance2[$unit->getId()] = Board::getReachableCellsAtDistanceReserve($unit, 2);
          // get reachable cells at distance 1 equivalent to neighbours but with valid move
          $cells_wire_deployement2[$unit->getId()] = Board::getReachableCellsAtDistanceReserve($unit, 1);
          $cells_wire_deployement[$unit->getId()] = array_filter($cells_wire_deployement2[$unit->getId()],
            function ($c) {
              return !isset($c['source']) || !$c['source'];
            }
          );
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
          // add cells list for wire (cells adjacent to units)
          'cells_wire_deployement' => $cells_wire_deployement,
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
    $elem = null, $isWild = false, $onStageArea = false, $unitId = null, $miscArgs = null)
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

        case 'wire':
          //$unit = $player->getTeam()->getUnits()[0];
          foreach ($miscArgs as $key => $cell) {
            $wire = Terrains::add([
              'type' => 'wire',
              'tile' => 'wire',
              'x' => $cell['x'],
              'y' => $cell['y'],
              'orientation' => 1,
            ]);
            Notifications::addTerrain(
              $player,
              $wire,
              \clienttranslate('${player_name} reinforces their position by placing a wire (in ${coordSource})')
            );
          }
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

        case 'airpowertoken':
          //get one Air Power Token for playerId'team
          $player = Players::get($pId);
          $teamId = $player->getTeam()->getId();
          $teamToken = Globals::getAirPowerTokens();
          if(!is_null($teamToken)) {
            array_push($teamToken, $teamId);
          } else {
            $teamToken[] = $teamId;
          }
          Globals::setAirPowerTokens($teamToken);
          Notifications::addAirpowerToken($player);
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
      $fullListToDeploy[$pId] = $listToDeploy;
      Globals::setRollReserveList($fullListToDeploy);
      
      // condition if there are still elements at no cost
      $elementNoCost = ['sandbag','advance2','airpowertoken','wire'];
      $isElementNoCostToDeploy = false;
      if (!empty($listToDeploy)) {
        foreach ($listToDeploy as $elem) {
          if (in_array($elem,$elementNoCost)) {
            $isElementNoCostToDeploy = true;
          }
        }
      }
      
      // deployement may continue if there are remaining reserve tokens
      // and if there are still unit or elements to be deployed or
      // if there are still element at no cost if no remaining tokens
      if (($player->getTeam()->getNReserveTokens() > 0 && !empty($listToDeploy))
        || ($player->getTeam()->getNReserveTokens() <= 0 && $isElementNoCostToDeploy)){
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
    if (Globals::getCampaignStep() > 0) {
      // Refresh interface before any reserve roll deployement for 2nd scenario (like $forceRefresh or $rematch case)
      $datas = Game::get()->getAllDatas();
      unset($datas['prefs']);
      unset($datas['discard']);
      unset($datas['canceledNotifIds']);
      unset($datas['localPrefs']);
      Notifications::refreshInterface($datas);
    }

    if (!Globals::getRollReserveDone()) {
      $list = [];
      foreach(Teams::getAll() as $team) {
        $player = $team->getCommander();
        
        $elementsToDeploy = self::ReserveRoll($player);
        // remove elements which are costing 1 token, if team has no token available
        // keep only elements at no cost 
        if ($team->getReserveTokens() == 0) {
          $elementNoCost = ['sandbag','advance2','airpowertoken','wire'];
          $elementsToDeploy2 = array_filter($elementsToDeploy, function ($elem) use ($elementNoCost)  {
            return in_array($elem, $elementNoCost);
          });
          $list[$player->getId()] = $elementsToDeploy2;
        } else {
          $list[$player->getId()] = $elementsToDeploy;
        }
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
    $scenarioId = Scenario::getId();
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
    if (Globals::isCampaign()) {
      // Campaign mode case
      // get winner and check Replenish reserve tokens
      $team = Teams::getWinner();
      $campaign = Globals::getCampaign();
      $step = Globals::getCampaignStep();
      // TODO store last winner in Globals Campaign
      $campaign['winners'][$step] = $team->getId();
      Globals::setCampaign($campaign);
      $nextstep = $campaign['scenarios'][$team->getId()][$step];
      if ($nextstep == 'END') {
        $nextstep = INFINITY;
      }
      Globals::setCampaignStep($nextstep); // increment Campaign step according to campaign order by team
     
      // Check if remaining tokens or units on staging area, 
      // if so add the token back to the reserve token counter
      $nbunitsStillOnReserve = count(Units::getOfTeamOnReserve($team->getId()));
      $team->incNReserveTokens($nbunitsStillOnReserve);
      // remove all Air Power Tokens
      Globals::setAirPowerTokens([]);
      // and notify all players
      Notifications::replenishWinnerReserveTokens($team, $nbunitsStillOnReserve);
      //Store current tokens in Globals or set them back to 0 if END of Campaign Round
      $teams = Teams::getAll();
      foreach ($teams as $t) {
        $campaign['scenarios'][$t->getId()]['reserve_tokens']['current'] = $nextstep == INFINITY ? 0 :  $t->getReserveTokens();        
      }
      Globals::setCampaign($campaign);

      //temporary next state for testing :
      $this->gamestate->nextState('next_scenario');

    } else {
      // standard Case
      $round = Globals::getRound();
      $maxRound = Globals::isTwoWaysGame() ? 2 : 1;
      if ($round == $maxRound) {
        $this->gamestate->jumpToState(\ST_END_OF_GAME);
      } else {
        $this->gamestate->setAllPlayersMultiactive();
        $this->gamestate->nextState('change');
      }
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

  public function stNextCampaignScenario() 
  {
    // Delete previous Scenario from DB Globals
    Globals::setScenario(null);
    Globals::setScenarioId(null);
    // Select next scenario to play, load scenario and init it
    $step = Globals::getCampaignStep();

    // case END of Campaign :
    if ($step == \INFINITY) {
      // If this is END of campaign, we switch to round 2 or End the game if Round 2
      $round = Globals::getRound();
      $maxRound = Globals::isTwoWaysGame() ? 2 : 1;
      if ($round == $maxRound) {
        $this->gamestate->jumpToState(\ST_END_OF_GAME);
      } else {
        // case end of round 1        
        $scenarioId = Globals::getCampaign()['scenarios']['list'][0];
        Globals::setScenarioId($scenarioId);
        Scenario::loadId($scenarioId);
        // In this case, Round will be incremented at next ST NEW ROUND so finaly will be round 2(keep it to remember)
        Globals::setRound(1);
        // Restart Campaign for round 2 at campaign step 0
        Globals::setCampaignStep(0);
      }
    } else {
      $scenarioId = Globals::getCampaign()['scenarios']['list'][$step];
      Globals::setScenarioId($scenarioId);
      Scenario::loadId($scenarioId);
      // In this case Round will be incremented at next ST NEW ROUND, in this case final Round will stay the same
      Globals::incRound(-1); 
    }

    // Next state ST_CHANGE_OF ROUND in order to See intermediate scoresof curent scenario before next scenario
    $this->gamestate->setAllPlayersMultiactive();
    $this->gamestate->nextState('done');
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
