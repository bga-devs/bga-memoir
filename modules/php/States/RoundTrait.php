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
    Globals::setRollVictoryEventDone(false);
    Globals::setArmorBreakthroughDone(['AXIS' => false, 'ALLIES' => false]);
    Globals::setDistributedCards(['AXIS' => [], 'ALLIES' => []]); // Overlord
    Globals::setInitHandDone(false);

    if (!Globals::isCampaign()) {
      Globals::setInitHandDone(true);
      Globals::setAirPowerTokens(null);
      Globals::setAirPowerTokenUsed(false);
    }

   // TODO only once per round
   $options = Scenario::getOptions();
   // Case if Campaign and one scenario has airdrop option or smoke screen option
   // will be checked in ST_RECHECK_BEFORE_FIRST TURN after Reserve Roll Deployement
    if (Globals::isCampaign()) {
      $team = Teams::get(Globals::getTeamTurn());

      // TO DO
      if(Globals::getCampaignStep() > 0) {
        $this->gamestate->nextState('victoryEventRoll');
      } else {
        $this->gamestate->setAllPlayersMultiactive();
        $this->gamestate->nextState('reserveRoll');
      }
    } elseif (isset($options['airdrop'])) {
      // Check for options
      if (isset($options['airdrop'])) {
        $team = Teams::get($options['airdrop']['side']);
        $this->changeActivePlayerAndJumpTo($team->getCommander(), ST_AIR_DROP);
        return;
      }
      
    } else {

      if (isset($options['smoke_screen'])) {
        $team = Teams::get($options['smoke_screen']['side']);
        $this->changeActivePlayerAndJumpTo($team->getCommander(), ST_SMOKE_SCREEN);
        return;
      }

      $this->gamestate->nextState('prepareTurn');
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
    /*if (Globals::getCampaignStep() > 0) {
      // Refresh interface on first scenario
      $datas = Game::get()->getAllDatas();
      unset($datas['prefs']);
      unset($datas['discard']);
      unset($datas['canceledNotifIds']);
      unset($datas['localPrefs']);
      Notifications::refreshInterface($datas);
    }*/

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

  public function stVictoryEventRoll() {

    $round = Globals::getRound();
    if (Globals::getCampaignStep() > 0 && !($round == 2)) {
      // Refresh interface before any reserve roll deployement for 2nd scenario (like $forceRefresh or $rematch case)
      $datas = Game::get()->getAllDatas();
      unset($datas['prefs']);
      unset($datas['discard']);
      unset($datas['canceledNotifIds']);
      unset($datas['localPrefs']);
      Notifications::refreshInterface($datas);
    }
    Notifications::message(clienttranslate('Victory Event Roll'));

    // get numbers of dice per player and roll them
    if (!Globals::getRollReserveDone()) {
      $list = [];
      foreach(Teams::getAll() as $team) {
        $player = $team->getCommander();
        $nDice = 2;
        $campaign = Globals::getCampaign();
        $winners = $campaign['winners'] ?? [];
        foreach ($winners as $winner) {
          if ($winner == $player->getTeam()->getId()) {
            $nDice += 1;
          }
        }      
        
        $eventsList= $this->victoryRoll($player, $nDice);
        $list[$player->getId()] = $eventsList;
      }
      // determine team and player order
      $activePlayer = Players::getActive();
      $activeTeamId = $activePlayer->getTeam()->getId();
      $teamOrder = $activeTeamId == ALLIES ? [ALLIES, AXIS] : [AXIS, ALLIES];
      $finalList = [];
      foreach ($teamOrder as $tId) {
        $pId = Teams::get($tId) -> getCommander() -> getId();
        $oppId = Teams::get($tId) -> getOpponent() -> getCommander() -> getId();
        $playerList = $list[$pId];
        foreach ($playerList as $result) {
          if ($result == 'wild') {
            $finalList[] = ['action' => 'wild', 'player' => $pId];
          } else {
            $finalList[] = ['action' => $result, 'player' => $oppId];
          }
        }
      }

      Globals::setRollVictoryEventList($finalList);
      Globals::setRollVictoryEventDone(true);
    }

    $actionList = Globals::getRollVictoryEventList();
    $currentAction = $actionList[array_key_first($actionList)];
    $nextPlayerId = $currentAction['player'];
    $this->changeActivePlayerAndJumpTo($nextPlayerId, ST_VICTORY_EVENT_RESOLUTION);
  }

  public function victoryRoll($player, $nDice) {
    $victoryRollList = [];
    $results = Dice::roll($player, $nDice, null, false);

    $victoryRollMap = [
      \DICE_INFANTRY => 'inf',
      \DICE_ARMOR => 'tank',
      \DICE_GRENADE => 'wild',
      \DICE_FLAG => 'retreat',
      \DICE_STAR => 'card'];

    $rollPriorityMap = [
      0 => \DICE_INFANTRY,
      1 => \DICE_ARMOR,
      2 => \DICE_STAR,
      3 => \DICE_FLAG,
      4 => \DICE_GRENADE
    ];

    // Sort victory event roll result by priority type
    for ($i=0; $i < 5; $i++) { 
      foreach ($results as $d) {
        if ($d == $rollPriorityMap[$i]) {
          $victoryRollList[] = $victoryRollMap[$d];
        }
      }
    }

    return $victoryRollList;
  }

  public function argsVictoryEventResolution() {
    // args are Player to perform the action, list of units of this player
    // specific action for card to be defined (like scenario Pegasus Bridge)
    $actionList = Globals::getRollVictoryEventList();
    $currentAction = $actionList[array_key_first($actionList)];
    $pId = $currentAction['player'];
    $actionType = $currentAction['action'];

    // get all Inf and Armor units of opponent player (this player is already sorted from previous state)
    $player = Players::get($pId);
    $units = $player -> getUnits() -> toArray();
    $unitsPos = [];
    foreach ($units as $unit) {
      $unitId = $unit->getId();
      $unitsPos[$unitId]['pos'] = $unit -> getPos();
      $unitsPos[$unitId]['retreat_args'] = Board::getArgsRetreat($unit, 1, 1);
    }

    $oppunits = $player -> getTeam() -> getOpponent() -> getCommander() -> getUnits() -> toArray();
    $infUnits = array_filter($units, function ($unit) {
      return $unit->getType() == \INFANTRY;});
    $infPos = [];
    foreach ($infUnits as $unit) {
      $infPos[] = $unit -> getPos();
    }
    $tankUnits = array_filter($units, function ($unit) {
      return $unit->getType() == \ARMOR;});
    $tankPos = [];
    foreach ($tankUnits as $unit) {
      $tankPos[] = $unit -> getPos();
    }
    $fullStrengthUnits = array_filter($oppunits, function ($unit) {
      return !($unit->isWounded()) && !($unit instanceof \M44\Units\Sniper);});
    $fullStrengthPos = [];
    foreach ($fullStrengthUnits as $unit) {
      $fullStrengthPos[] = $unit -> getPos();
    }
    
    $args = [
          'player' => $pId,
          'action_type' => $actionType, 
          'inf_units' => $infPos,
          'tank_units' => $tankPos,
          'full_strength_units' => $fullStrengthPos,
          'retreat_units' => $unitsPos,
          'titleSuffix' => $actionType,
      ];

    return $args;
  }

  public function actVictoryEventResolution($x, $y, $actionPerformed, $retreat_cell) {
    self::checkAction('actVictoryEventResolution');
    // deal with current action type results
    $actionList = Globals::getRollVictoryEventList();
    $currentAction = $actionList[array_key_first($actionList)];
    $player = Players::get($currentAction['player']);
    $oppPlayer = $player -> getTeam() -> getOpponent() -> getCommander();
    
    switch ($actionPerformed) {
      case 'inf':
      case 'tank':
        $unit = Board::getUnitInCell($x, $y);
        $realHits = $unit->takeDamage(1);
        Notifications::takeDamage($player, $unit, 1, false);
        break;
      
      case 'wild':
        $unit = Board::getUnitInCell($x, $y);
        $realHits = $unit->takeDamage(1);
        Notifications::takeDamage($oppPlayer, $unit, 1, false);
        break;

      case 'card':
        $scenario = Scenario::get();
        $teamId = $player -> getTeam() -> getId();
        $card_player = $scenario['game_info']['side_player1'] == $teamId ? 'cards_player1' : 'cards_player2';
        $scenario['game_info'][$card_player] -= 1;
        $team = $player -> getTeam();
        $team -> incNCards(-1);

        $mustDraw_player = $scenario['game_info']['side_player1'] == $teamId ? 'mustDraw_player1' : 'mustDraw_player2';
        if (!isset($scenario['game_info'][$mustDraw_player])) {
          $scenario['game_info'][$mustDraw_player] = 1;
        } else {
          $scenario['game_info'][$mustDraw_player] += 1;
        }
        Globals::setScenario($scenario);
        break;

      // 'retreat' cases
      case 'retreat':
        if (empty($retreat_cell)) { // can not retreat result, take 1 hit
          $unit = Board::getUnitInCell($x, $y);
          $realHits = $unit->takeDamage(1);
          Notifications::takeDamage($player, $unit, 1, false);
        } else {
          // move retreat 1 move
          $unit = Board::getUnitInCell($x,$y);
          $coordSource = $unit -> getPos();
          $coordRetreat = [
            'x' => $retreat_cell['x'],
            'y' => $retreat_cell['y'],
          ];
          Notifications::retreatUnit($unit->getPlayer(), $unit, $coordSource, $coordRetreat);
          $tmp = Board::moveUnit($unit, $coordRetreat, true);
        }
        break;
      
      default:
        var_dump('Case not coded so far, should not happen');
        break;
    }

    // remove latest action from the action list and check if list is empty
    if ($currentAction['action'] == $actionPerformed) {
      $removeAction = array_shift($actionList);
      Globals::setRollVictoryEventList($actionList);
    } else {
      throw new \BgaVisibleSystemException(
        'Should not happen. Please create a bug report at this exact point in the game with details on what you were trying to do'
      );
    }
    
    if (!empty($actionList)) {
      $nextAction = $actionList[array_key_first($actionList)];
      $nextPlayerId = $nextAction['player'];
      $this->changeActivePlayerAndJumpTo($nextPlayerId, ST_VICTORY_EVENT_RESOLUTION);
    } else {
      // next state management if finished
      $this->gamestate->setAllPlayersMultiactive();
      $this->gamestate->nextState('reserveRoll');
    }
    
  }

  public function stVictoryEventResolution() {
    $args = $this->argsVictoryEventResolution();

  }

  public function stRecheckBeforeFirstTurn() {
    $options = Scenario::getOptions();
   // Case if Campaign and one scenario has airdrop option or smoke screen option (as reserve roll was executed)
    if (Globals::isCampaign() && Globals::getRollReserveDone()) {
      // Check for options

      if (isset($options['airdrop'])) {
        $team = Teams::get($options['airdrop']['side']);
        $this->changeActivePlayerAndJumpTo($team->getCommander(), ST_AIR_DROP);
        return;
      }

      if (isset($options['smoke_screen'])) {
        $team = Teams::get($options['smoke_screen']['side']);
        $this->changeActivePlayerAndJumpTo($team->getCommander(), ST_SMOKE_SCREEN);
        return;
      }
 
      $this->gamestate->nextState('prepareTurn');
    }
  }

  public function argsSmokeScreen() {
    $cells = Board::getListOfCells();
    return ['cells' => $cells];
  }

  public function actSmokeScreen($cells, $smokescreen) {
    // add one smoke screen per each hexes selected
    self::checkAction('actSmokeScreen');

    if ($smokescreen) {
      foreach($cells as $cell) {
        // add smoke screen terrain in the database
        $smokeScreen = Terrains::add([
            'type' => 'smokescreen',
            'tile' => 'smoke0',
            'x' => $cell['x'],
            'y' => $cell['y'],
            'orientation' => 0,
          ]);

        // display full smoke screen on the cells
        $player = Players::getActive();
        Notifications::addTerrain(
              $player,
              $smokeScreen,
              \clienttranslate('${player_name} places a smoke screen in ${coordSource}')
            );
      }
    }

    // state transition
    $this->gamestate->jumpToState(ST_PREPARE_TURN);


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

      // update the campaign scores in Globals variable after each round for each player
      $round = Globals::getRound();
      $campaign = Globals::getCampaign();
      $step = Globals::getCampaignStep();
      foreach (Players::getAll() as $player) {
        $teamId = $player -> getTeam() ->getId();
        // set campaign step results of the current round
        $scoreTeam = $campaign['scenarios'][$teamId]['score'];

        $medalStepRound = $player->getStat('medalRound' . $round);
        if($step > 0) {
          for ($i=0 ; $i < $step ; $i++) { 
            $medalStepRound = $medalStepRound - $scoreTeam[$round][$i];
          }
        }

        $campaign['scenarios'][$teamId]['score'][$round][$step] = $medalStepRound + 0;

        // set campaign total medal round of the current round
        $totalMedalsName = 'get' . 'MedalRound' . $round;
        $totalMedals = Stats::$totalMedalsName($player);
        $campaign['scenarios'][$teamId]['score'][$round]['total'] = $totalMedals;
        
        // set total objectives medals
        $nUnitsMedals = 0;
        foreach (['inf', 'armor', 'artillery', 'other'] as $type) {
          $nUnitsMedals += $player->getStat($type . 'UnitRound' . $round);
        }
        $objectivesMedals = $player->getStat('medalRound' . $round) - $nUnitsMedals;
        $campaign['scenarios'][$teamId]['score'][$round]['objectives_medals'] = $objectivesMedals;
        
        // set campaign objective track bonus according to objective track
        // if objectives are above max bonus allow only max bonus track points
        $objectivePoints = Globals::getCampaign()['scenarios'][$teamId]['objectives_points'];
        $objectivesMax = array_key_last($objectivePoints);
        $objectivesBonus = $objectivesMedals > $objectivesMax ? $objectivePoints[$objectivesMax] : $objectivePoints[$objectivesMedals];
        $campaign['scenarios'][$teamId]['score'][$round]['objectives_bonus'] = $objectivesBonus;

        // set total victory points : total medals + objective track bonuses
        $victoryPoints = $totalMedals + $objectivesBonus;
        $campaign['scenarios'][$teamId]['score'][$round]['victory_points'] = $victoryPoints;

        Globals::setCampaign($campaign);
      }
      Notifications::updateCampaignScore();

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
        // reset winners list from campaign mode (prevent having more victory event roll dice from the 1st round)
        $campaign = Globals::getCampaign();
        $campaign['winners'] = [];
        Globals::setCampaign($campaign);      
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
