<?php

namespace M44;

use M44\Cards\Breakthrough\InfantryAssault;
use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Core\Preferences;
use M44\Helpers\Utils;
use M44\Managers\Cards;
use M44\Managers\Players;
use M44\Managers\Terrains;
use M44\Managers\Medals;
use M44\Managers\Teams;
use M44\Managers\Units;
use M44\Managers\Tokens;
use M44\Scenario;

const LINE_INTERSECTION = 0;
const LINE_TANGENT_LEFT = 1;
const LINE_TANGENT_RIGHT = 2;
const LINE_CORNER = 3;

class Board
{
  public static $dimensions = [
    STANDARD_DECK => ['x' => 13, 'y' => 9],
    BREAKTHROUGH_DECK => ['x' => 13, 'y' => 17],
    OVERLORD_DECK => ['x' => 26, 'y' => 9],
  ];

  protected static $grid = [];
  protected static $hillComponents = null;
  protected static $mountainComponents = null;
  protected static $caveComponents = null;
  public static function init()
  {
    // Try to fetch scenario from DB
    $scenario = Scenario::get();
    if (is_null($scenario)) {
      return;
    }

    // Create the board
    self::$grid = self::createGrid();
    foreach (self::$grid as $x => $col) {
      foreach ($col as $y => $cell) {
        self::$grid[$x][$y] = [
          'terrains' => [],
          'unit' => null,
          'labels' => [],
          'tokens' => [],
        ];
      }
    }

    // Add the terrains
    foreach (Terrains::getAllOrdered() as $terrain) {
      self::$grid[$terrain->getX()][$terrain->getY()]['terrains'][] = $terrain;
    }

    // Add the units on the board not on the reserve
    foreach (Units::getAllOrdered() as $unit) {
      if ($unit->getNUnits() > 0) {
        self::$grid[$unit->getX()][$unit->getY()]['unit'] = $unit;
      }
    }

    // Add the labels
    foreach ($scenario['board']['labels'] ?? [] as $labels) {
      foreach ($labels['text'] ?? [] as $label) {
        self::$grid[$labels['col']][$labels['row']]['labels'][] = $label;
      }
    }

    // Add the medals/tokens
    foreach (Tokens::getAll() as $token) {
      self::$grid[$token['x']][$token['y']]['tokens'][] = $token;
    }

    self::$hillComponents = null;
    self::$mountainComponents = null;
  }

  /////////////////////////////////////////
  //  ____       _   _
  // / ___|  ___| |_| |_ ___ _ __ ___
  // \___ \ / _ \ __| __/ _ \ '__/ __|
  //  ___) |  __/ |_| ||  __/ |  \__ \
  // |____/ \___|\__|\__\___|_|  |___/
  /////////////////////////////////////////

  public static function removeTerrain($terrain)
  {
    $x = $terrain->getX();
    $y = $terrain->getY();
    Utils::filter(self::$grid[$x][$y]['terrains'], function ($t) use ($terrain) {
      return $t->getId() != $terrain->getId();
    });
    Terrains::remove($terrain);
  }

  public static function addTerrain($terrain)
  {
    self::$grid[$terrain->getX()][$terrain->getY()]['terrains'][] = $terrain;
  }

  public static function addUnit($unit)
  {
    self::$grid[$unit->getX()][$unit->getY()]['unit'] = $unit;
  }

  public static function removeUnit($unit)
  {
    self::$grid[$unit->getX()][$unit->getY()]['unit'] = null;

    // Check for listeners
    foreach ((self::$grid[$unit->getX()][$unit->getY()]['terrains'] ?? []) as $terrain) {
      $terrain->onUnitEliminated($unit);
    }

    // Check for potential lost medals
    Medals::checkBoardMedals(false);

    if (Teams::checkVictory()) {
      return;
    }
  }

  /////////////////////////////////////////
  //    ____      _   _
  //  / ___| ___| |_| |_ ___ _ __ ___
  // | |  _ / _ \ __| __/ _ \ '__/ __|
  // | |_| |  __/ |_| ||  __/ |  \__ \
  //  \____|\___|\__|\__\___|_|  |___/
  /////////////////////////////////////////

  public static function getUiData()
  {
    $scenario = Scenario::get();
    if (is_null($scenario)) {
      return null;
    }
    return [
      'type' => $scenario['board']['type'],
      'face' => $scenario['board']['face'],
      'grid' => self::$grid,
    ];
  }

  public static function getUnitInCell($x, $y = null)
  {
    if (is_null($y)) {
      $y = $x['y'];
      $x = $x['x'];
    }
    return self::$grid[$x][$y]['unit'];
  }

  public static function getTerrainsInCell($x, $y = null)
  {
    if (is_null($y)) {
      $y = $x['y'];
      $x = $x['x'];
    }
    return self::$grid[$x][$y]['terrains'];
  }

  // Useful for close assault card
  public static function isAdjacentToEnnemy($unit)
  {
    foreach (self::getNeighbours($unit->getPos()) as $cell) {
      $t = self::$grid[$cell['x']][$cell['y']];
      if (!is_null($t['unit']) && $t['unit']->isOpponent($unit)) {
        return true;
      }
    }

    return false;
  }

  public static function isAdjacentToBeach($cell)
  {
    $isbeach = false;
    $neighbours = self::getNeighbours($cell); // Not accessible from Ocean.php as protected
    foreach ($neighbours as &$neigh) {
      if (self::isBeachCell($neigh)) {
        $isbeach = true;
      }
    }
    unset($neigh);
    return !$isbeach;
  }

  public static function hasFullRailPath($path)
  {
    $ispathrail = true;
    $cells = $path['cells'];

    foreach ($cells as $c) {
      if ($ispathrail && !self::isRailCell($c)) {
        $ispathrail = false;
      }
    }
    return $ispathrail;
  }


  /**
   * Do a generic OR on a given property of all terrains on the cell
   */
  public static function cellHasProperty($cell, $property, $unit)
  {
    /*if($unit->isOnReserveStaging()) {
      return false;
    }*/
    $t = self::$grid[$cell['x']][$cell['y']];
    foreach ($t['terrains'] as $terrain) {
      if ($terrain->$property($unit)) {
        return true;
      }
    }
    return false;
  }

  /**
   * Do some magic to call self::{$property}Cell that will call cellHasProperty
   *  eg: self::isImpassableCell will call cellHasProperty(..., 'isImpassable', ...)
   */
  public static function __callStatic($method, $args)
  {
    if (preg_match('/^([a-zA-Z]+)Cell$/', $method, $match)) {
      $prop = $match[1];
      $cell = $args[0];
      $unit = $args[1] ?? null;
      return self::cellHasProperty($cell, $prop, $unit);
    }
  }

  // Useful for DigIn card
  public static function canPlaceSandbag($unit)
  {
    return !self::cellHasProperty($unit->getPos(), 'isBlockingSandbag', $unit);
  }

  /////////////////////////////////
  //  __  __  _____     _______
  // |  \/  |/ _ \ \   / / ____|
  // | |\/| | | | \ \ / /|  _|
  // | |  | | |_| |\ V / | |___
  // |_|  |_|\___/  \_/  |_____|
  /////////////////////////////////

  /**
   * $realMove : useful for "desert rule" that is kind of move but not really
   */
  public static function getReachableCells($unit, $force = false, $realMove = true)
  {
    // Already moved before ?
    $uId = Globals::getUnitMoved();
    if (!$force && ($unit->getMoves() > 0 || $unit->hasUsedRoadBonus()) && $uId != -1 && $uId != $unit->getId()) {
      return [self::getCurrentPosAttackInfo($unit)];
    }

    // Compute remaining moves for the unit
    $maxDistance = $unit->getMovementRadius();
    $card = $unit->getActivationOCard();
    if ($card != null) {
      if (($card->isType(CARD_BEHIND_LINES)
          || $card->isType(\CARD_COUNTER_ATTACK)
          && $card->getExtraDatas('copiedCardType') == \CARD_BEHIND_LINES)
        && $unit->getType() == INFANTRY
      ) {
        $maxDistance = 3; // Units activated by "BehindEnemyLines" can moves up to 3 hexes
      }
      // Only effect on unit with a move radius of 2 or less
      elseif ($card->isType(CARD_INFANTRY_ASSAULT) && $unit->getType() == INFANTRY && $unit->getMovementRadius() <= 2) {
        $maxDistance++;
      } elseif ($card->isType(CARD_ARTILLERY_BOMBARD) && $unit->getType() == \ARTILLERY) {
        $maxDistance = 3;
      }
    }

    $m = $maxDistance - $unit->getMoves();
    return self::getReachableCellsAtDistance($unit, $m, $realMove);
  }

  protected static function getCurrentPosAttackInfo($unit)
  {
    // Add current pos of unit
    $startingCell = $unit->getPos();
    if (!empty(self::getTargetableCells($unit, $startingCell, 0))) {
      $startingCell['canAttack'] = true;
    }
    $startingCell['source'] = true;
    return $startingCell;
  }

  /**
   * getReachableCellsAtDistance: find all the cells reachable for movements
   *   - $unit : a Unit object, used to compute starting pos and movement costs
   *   - $d : max distance we are looking for
   */
  public static function getReachableCellsAtDistance($unit, $d, $realMove = true)
  {
    if ($unit->isStopped()) {
      return [self::getCurrentPosAttackInfo($unit)];
    }
    //$d -= $unit->getMovesOnTheRoad();

    $startingCell = $unit->getPos();
    $stagingarea = $unit->isOnReserveStaging();
    list($cells, $markers) = self::getCellsAtDistance(
      $startingCell,
      $d,
      function ($source, $target, $d) use ($unit, $realMove) {
        $cost = self::getDeplacementCost($unit, $source, $target, $d, false, false, $realMove);
        return min(INFINITY, $cost);
      },
      function ($cell) use ($unit) {
        return self::avoidIfPossibleCell($cell, $unit) ? 1 : 0;
      },
      true,
      $stagingarea
    );

    // Compute road paths with bonus of 1 move if starting pos is on road
    if ($realMove && self::isRoadCell($startingCell, $unit) && $unit->stayedOnRoad()) {
      $d2 = $d + $unit->getRoadBonus();
      list($cells2, $markers2) = self::getCellsAtDistance($startingCell, $d2, function ($source, $target, $d) use (
        $unit
      ) {
        return self::getDeplacementCost($unit, $source, $target, $d, false, true);
      });
      // Reduce cost by 1 if bonus not used
      foreach ($cells2 as &$cell) {
        $cell['road'] = true;
        if ($unit->getRoadBonus() != 0) {
          $cell['d'] -= $unit->getRoadBonus();
          foreach ($cell['paths'] as &$path) {
            if (!empty($path)) {
              $path['cells'][0]['cost'] -= $unit->getRoadBonus();
            }
          }
        }
      }

      // Merge with other matching cell, avoiding duplicates
      foreach ($cells as $oldCell) {
        if (Utils::searchCell($cells2, $oldCell) === false) {
          $cells2[] = $oldCell;
        }
      }
      $cells = $cells2;
    }

    // Filter out paths if needed
    foreach ($cells as &$cell) {
      Utils::filter($cell['paths'], function ($path) use ($unit, $cell) {
        if ($unit->getType() == DESTROYER) {
          // specific unit in ocean cannot move to adjacent hexes to beach
          return self::isAdjacentToBeach($cell);
        } elseif (in_array($unit->getType(), [LOCOMOTIVE, WAGON])) {
          // train unit can only move on rail paths
          return self::hasFullRailPath($path);
        } else {
          return self::isValidPath($unit, $cell, $path);
        }
      });
    }
    // Filter out cells without paths
    Utils::filter($cells, function ($cell) {
      return !empty($cell['paths']);
    });

    // Compute for each cell whether the unit might be able to attack after the move
    foreach ($cells as &$cell) {
      if (!empty(self::getTargetableCells($unit, $cell, $cell['d']))) {
        $cell['canAttack'] = true;
      }
      if (self::mustStopWhenEnteringCell($cell, $unit) || self::mustStopMovingWhenEnteringCell($cell, $unit)) {
        $cell['stop'] = true;
      }
      if (self::enteringCannotBattleCell($cell, $unit)) {
        $cell['noAttack'] = true;
      }
    }

    $cells[] = self::getCurrentPosAttackInfo($unit);
    return $cells;
  }

  public static function getReachableCellsAtDistanceReserve($unit, $d, $realMove = true)
  {

    $startingCell = $unit->getPos();
    
    list($cells, $markers) = self::getCellsAtDistance(
      $startingCell,
      $d,
      function ($source, $target, $d) use ($unit, $realMove) {
        $cost = self::getDeplacementCost($unit, $source, $target, $d, false, false, $realMove);
        return min(INFINITY, $cost);
      },
      function ($cell) use ($unit) {
        return self::avoidIfPossibleCell($cell, $unit) ? 1 : 0;
      },
      true
    );

    // Filter out paths if needed
    foreach ($cells as &$cell) {
      Utils::filter($cell['paths'], function ($path) use ($unit, $cell) {
        return self::isValidPath($unit, $cell, $path);
      });
    }
    // Filter out cells without paths
    Utils::filter($cells, function ($cell) {
      return !empty($cell['paths']);
    });

    // Compute for each cell whether the unit might be able to attack after the move
    foreach ($cells as &$cell) {
      if (!empty(self::getTargetableCells($unit, $cell, $cell['d']))) {
        $cell['canAttack'] = true;
      }
      if (self::mustStopWhenEnteringCell($cell, $unit) || self::mustStopMovingWhenEnteringCell($cell, $unit)) {
        $cell['stop'] = true;
      }
      if (self::enteringCannotBattleCell($cell, $unit)) {
        $cell['noAttack'] = true;
      }
    }

    $cells[] = self::getCurrentPosAttackInfo($unit);
    return $cells;
  }

  /**
   * getDeplacementCost: return the cost for a unit to move from $source to an adjacent $target,
   *    given the fact that the unit can move at most $d hexes
   */
  public static function getDeplacementCost(
    $unit,
    $source,
    $target,
    $d,
    $takeGround = false,
    $roadOnly = false,
    $realMove = true
  ) {
    // Get corresponding cells
    $sourceCell = self::$grid[$source['x']][$source['y']];
    $targetCell = self::$grid[$target['x']][$target['y']];

    // If we are computing ROAD only and target is not a road, abort
    if ($roadOnly && $realMove) {
      if (!self::isRoadCell($target, $unit)) {
        return \INFINITY;
      }
      // We must also make sure that the road are connected
      foreach ($targetCell['terrains'] as $terrain) {
        if ($terrain->isRoad($unit) && !$terrain->isLinked($source, $unit)) {
          return INFINITY;
        }
      }
    }

    // If there is a unit => can't go there
    if (!is_null($targetCell['unit']) && !$targetCell['unit']->isOnReserveStaging()) {
      return \INFINITY;
    }
    
    // if it's a cave && not a neighbour: JP can teleport, other can go if near
    if (
      $unit->getNation() == 'jp' &&
      $unit->getType() == \INFANTRY 
    ) {
      if (!in_array(['x' => $target['x'], 'y' => $target['y']], self::getNeighbours($source))) {
        foreach ($targetCell['terrains'] as $terrain) {
          if ($terrain->isCave($unit)) {
            if ($unit->getActivationOcard()->isType(CARD_INFANTRY_ASSAULT)) {
              return 2;
            } else {
              return 1;
            }
          }
        }
      } else { // if it's cave and neighbour to previous one
        foreach ($targetCell['terrains'] as $terrain) {
          if ($terrain->isCave($unit)) {
            return 1;
          }
        }
      }      
    }

    if (isset($source['teleportation']) && $source['teleportation'] == true) {
      return INFINITY; 
    }

    // check to forbid caves teleportation
    if (!in_array(['x' => $target['x'], 'y' => $target['y']], self::getNeighbours($source))) {
      return INFINITY;
    }

    // If there is an impassable terrain => can't go there
    if (self::isImpassableCell($target, $unit)) {
      return \INFINITY;
    }

    // If my unit cannot leave the hex (bunker & artillery)
    if (self::cantLeaveCell($source, $unit)) {
      return \INFINITY;
    }

    // If the edge is not possible, return infinity
    foreach ($sourceCell['terrains'] as $terrain) {
      if ($terrain->isBlocked($target, $unit)) {
        return INFINITY;
      }
    }
    foreach ($targetCell['terrains'] as $terrain) {
      if ($terrain->isBlocked($source, $unit)) {
        return INFINITY;
      }
    }

    // Ask the terrains about entering/leaving costs
    $cost = 1;
    foreach ($sourceCell['terrains'] as $terrain) {
      $cost = max($cost, $terrain->getLeavingDeplacementCost($unit, $source, $target, $d, $takeGround));
    }
    foreach ($targetCell['terrains'] as $terrain) {
      $cost = max($cost, $terrain->getEnteringDeplacementCost($unit, $source, $target, $d, $takeGround));
    }

    // Units activated by "BehindEnemyLines" card have no terrain restriction
    $card = $unit->getActivationOCard();
    if (!is_null($card) &&
        ($card->isType(CARD_BEHIND_LINES)
        || $card->isType(\CARD_COUNTER_ATTACK)
        && $card->getExtraDatas('copiedCardType') == \CARD_BEHIND_LINES)
      && $unit->getType() == INFANTRY
    ) {
      return $cost == INFINITY ? INFINITY : ($realMove ? 2 - $unit->getRoadBonus() : 1);
    }

    // Check terrain restriction
    $hasMoved = $unit->getMoves() > 0 || $unit->hasUsedRoadBonus() || $unit->getGrounds() != 0;
    $notFirstMovement = $source['d'] > 0 || $source['x'] != $unit->getX() || $source['y'] != $unit->getY();
    if ($notFirstMovement || $hasMoved) {
      // If I'm coming from a 'must stop' terrain, can't go there unless dist = 0
      if (
        self::mustStopWhenEnteringCell($source, $unit) ||
        (self::mustStopMovingWhenEnteringCell($source, $unit) && !$takeGround)
      ) {
        return \INFINITY;
      }

      // If I'm going to a 'must be adjacent' terrain, can't go there unless dist = 0, even in take ground
      if (self::mustBeAdjacentToEnterCell($target, $unit)) {
        return \INFINITY;
      }
    }

    return min(INFINITY, $cost + ($realMove ? 1 - $unit->getRoadBonus() : 0));
  }

  /**
   * isValidPath : check whether a path for movements is valid or not
   */
  public static function isValidPath($unit, $cell, $path)
  {
    // All paths are valid for Behind ennemy lines
    $card = $unit->getActivationOCard();
    if (!is_null($card) &&
        ($unit->getActivationOCard()->isType(CARD_BEHIND_LINES)
        || $card->isType(\CARD_COUNTER_ATTACK)
        && $card->getExtraDatas('copiedCardType') == \CARD_BEHIND_LINES)
      && $unit->getType() == INFANTRY
    ) {
      return true;
    }

    // If I'm coming from a 'must stop when leaving' terrain, path should be of length 1
    if (self::mustStopWhenLeavingCell($unit->getPos(), $unit) && count($path['cells']) > 1) {
      return false;
    }

    $totalPath = array_merge([$unit->getPos()], $path['cells']);
    foreach ($totalPath as $node) {
      $t = self::$grid[$node['x']][$node['y']];
      foreach ($t['terrains'] as $terrain) {
        if (!$terrain->isValidPath($unit, $cell, $totalPath)) {
          return false;
        }
      }
    }

    return true;
  }

  /**
   * moveUnit : move a unit to another hex
   */
  public static function moveUnit($unit, $cell, $isRetreat = false, $isTakeGround = false)
  {
    $pos = $unit->getPos();
    $unit->moveTo($cell);
    $interrupted = false;
    self::$grid[$pos['x']][$pos['y']]['unit'] = null;
    self::$grid[$cell['x']][$cell['y']]['unit'] = $unit;

    // Check listener
    $sourceCell = self::$grid[$pos['x']][$pos['y']];
    $targetCell = self::$grid[$cell['x']][$cell['y']];
    foreach ($sourceCell['terrains'] as $terrain) {
      $terrain->onUnitLeaving($unit, $isRetreat, $cell, $pos);
    }
    Tokens::removeTargets($pos);
    Tokens::removeCamouflage($pos);
    foreach ($targetCell['terrains'] as $terrain) {
      if ($terrain->onUnitEntering($unit, $isRetreat, $isTakeGround) === true) {
        $interrupted = true;
      }
    }

    Medals::checkBoardMedals(false);
    return [$interrupted, Teams::checkVictory()];
  }

  //////////////////////////////////////////
  //    _  _____ _____  _    ____ _  __
  //    / \|_   _|_   _|/ \  / ___| |/ /
  //   / _ \ | |   | | / _ \| |   | ' /
  //  / ___ \| |   | |/ ___ \ |___| . \
  // /_/   \_\_|   |_/_/   \_\____|_|\_\
  //////////////////////////////////////////

  /**
   * getTargetableCells: compute the cells targetable by a unit
   *  - $unit : Unit obj
   *  - $cell : position of the unit, useful for computing moves that results in a position where unit can attack
   *  - $moves : number of moves done by unit, useful in that case again
   */
  public static function getTargetableCells($unit, $cell = null, $moves = null)
  {
    // On the move or on reserve staging=> can't fight
    if ($unit->isOnTheMove() || $unit->isOnReserveStaging()) {
      return [];
    }

    // Already attacked before ?
    $uId = Globals::getUnitAttacker();
    if ($unit->getFights() > 0) {
      if ($uId != -1 && $uId != $unit->getId()) {
        return [];
      } elseif ($unit->canBattleAndRemoveWire()) {
        $cells = [];
        foreach (self::getTerrainsInCell($unit->getPos()) as $terrain) {
          $actions = $terrain->getPossibleAttackActions($unit);
          foreach ($actions as $action) {
            $action['type'] = 'action';
            $action['terrainId'] = $terrain->getId();
            $cells[] = $action;
          }
        }
        // throw new \feException(print_r($cells));
        return $cells;
      }
    }

    // Check whether the unit moved too much to attack
    // if unit moved on road, we need to remove one move linked to the bonus
    $m = $unit->getMoves() + ($moves ?? 0);
    $hasMoved = $m != 0 || $unit->hasUsedRoadBonus() || $unit->getGrounds() != 0;

    // no attack possible for a unit that moved and should not
    if ($unit->getCannotBattleIfMoved() && $hasMoved) {
      return [];
    }

    $maxMoves = $unit->getMovementAndAttackRadius();
    $card = $unit->getActivationOCard();
    $banzai = false;
    if ($card !== null) {
      // only if movement and attack radius == 1
      if ($card->isType(CARD_INFANTRY_ASSAULT) && $unit->getType() == \INFANTRY && $maxMoves == 1) {
        $maxMoves++;
      } elseif (($card->isType(CARD_BEHIND_LINES)
          || $card->isType(\CARD_COUNTER_ATTACK)
          && $card->getExtraDatas('copiedCardType') == \CARD_BEHIND_LINES)
        && $unit->getType() == INFANTRY
      ) {
        $maxMoves = INFINITY;
      } elseif ($unit->getBanzai() === true && $m > $maxMoves) {
        $banzai = true;
      }
    }

    if ($m > $maxMoves && !$banzai) {
      return [];
    }

    $pos = $cell ?? $unit->getPos();
    // if ($m > 0) {
    if (!$unit->getIgnoreCannotBattle()) {
      foreach (self::getTerrainsInCell($pos) as $terrain) {
        // Check whether unit moved into a cell that prevent attack
        if ($terrain->enteringCannotBattle($unit) && $hasMoved) {
          return [];
        }
        // Check whether unit is in a cell that prevent attack
        if ($terrain->cannotBattle($unit, $m)) {
          return [];
        }
      }
    }

    // Compute cells at fire range
    $power = $unit->getAttackPower($pos);
    if ($banzai) {
      $power = [$power[0]]; // if banzai, unit can only attack in close assault
    }
    $range = count($power);
    list($cells, $markers) = self::getCellsAtDistance(
      $pos,
      $range,
      function ($source, $target, $d) {
        // check to forbid caves teleportation
        if (!in_array(['x' => $target['x'], 'y' => $target['y']], self::getNeighbours($source))) {
          return INFINITY;
        }
        return 1;
      },
      null,
      false
    );

    // Keep only the ones where an enemy stands
    // remove units that are camouflage where not close assault
    Utils::filter($cells, function ($cell) use ($unit) {
      $oppUnit = self::getUnitInCell($cell);
      return !is_null($oppUnit) && $oppUnit->isOpponent($unit) 
        && ($cell['d'] == 1 || !$oppUnit->isCamouflaged()) 
        && !$oppUnit->isOnReserveStaging();
    });

    // Keep only the cells in sight
    // if ($unit->mustSeeToAttack()) {
    Utils::filter($cells, function ($cell) use ($unit, $pos) {
      return self::isInLineOfSight($unit, $cell, $pos);
    });
    // }

    // check if unit must be adjacent to battle in
    Utils::filter($cells, function ($cell) use ($unit, $pos) {
      if (self::mustBeAdjacentToBattleCell($unit->getPos(), $unit) || self::mustBeAdjacentToBattleCell($cell, $unit)) {
        return $cell['d'] == 1;
      }
      return true;
    });

    // Compute the opponents in contact with the unit
    $inContact = array_values(
      \array_filter($cells, function ($cell) {
        return $cell['d'] == 1;
      })
    );
    if (!empty($inContact)) {
      $cells = $inContact; // If at least one in contact => must fight one of them
    }

    // filter only the ennemy than can be targeted
    Utils::filter($cells, function ($cell) use ($unit) {
      return $unit->targets()[self::getUnitInCell($cell)->getType()];
    });

    $visibility = Globals::getNightVisibility();
    Utils::filter($cells, function ($cell) use ($visibility) {
      return $cell['d'] <= $visibility;
    });

    // Compute shooting powers for the remaining cells
    foreach ($cells as &$cell) {
      $cell['dice'] = $power[$cell['d'] - 1] + $unit->getAttackModifier($cell);
      $offenseModifier = self::getDiceModifier($unit, $pos, false);
      $defenseModifier = self::getDiceModifier($unit, $cell, true);

      if ($unit->ignoreDefenseOnCloseAssault($unit) && $cell['d'] == 1) {
        $defenseModifier = 0;
      }
      if ($unit->ignoreDefense()) {
        $defenseModifier = 0;
      }

      $cardModifier = 0;
      if ($unit->getActivationOCard() != null) {
        $cardModifier = $unit->getActivationOCard()->getDiceModifier($unit, $cell);
      }

      if (!is_null($unit->getMaxMalus()) && $cell['d'] == 1) {
        if ($defenseModifier <= $unit->getMaxMalus()) {
          $defenseModifier = $unit->getMaxMalus();
        }
      }

      $cell['dice'] += $offenseModifier + $defenseModifier + $cardModifier;
    }
    // Keep only the cells with at least one attack dice
    Utils::filter($cells, function ($cell) {
      return $cell['dice'] > 0;
    });

    // Add special actions that can replace attacks
    if (is_null($moves)) {
      foreach (self::getTerrainsInCell($pos) as $terrain) {
        $actions = $terrain->getPossibleAttackActions($unit);
        foreach ($actions as $action) {
          $action['type'] = 'action';
          $action['terrainId'] = $terrain->getId();
          $cells[] = $action;
        }
      }
    }

    return $cells;
  }

  /**
   * Compute whether the unit can see the target cell or not
   */
  public static function isInLineOfSight($unit, $target, $source = null)
  {
    $source = $source ?? $unit->getPos();
    $path = self::getCellsInLine($source, $target);
    $blockedLeft = false;
    $blockedRight = false;

    // Checking wadi effect
    if (self::isBlockingWadi($unit, $target, $source, $path)) {
      return false;
    }

    foreach ($path as $cell) {
      // Starting and ending points are never blocking
      if (self::areSameCell($cell, $source) || self::areSameCell($cell, $target)) {
        continue;
      }

      // If the cell is not blocking the line of sight, skip
      if (!self::isBlockingLineOfSight($unit, $target, $cell, $path)) {
        continue;
      }

      // First case : intersection through the hex => direct block
      if (in_array($cell['type'], [LINE_INTERSECTION, LINE_CORNER])) {
        return false;
      }
      // Second case : tangent intersection => store whether it blocks left or right side
      elseif ($cell['type'] == LINE_TANGENT_LEFT) {
        $blockedLeft = true;
      } elseif ($cell['type'] == LINE_TANGENT_RIGHT) {
        $blockedRight = true;
      }
    }

    return !$blockedLeft || !$blockedRight;
  }

  /**
   * Return whether a given cell is blocking line of sight considering what is on the cell (terrains, units, ...)
   */
  public static function isBlockingLineOfSight($unit, $target, $cell, $path)
  {
    if (!self::isValidCell($cell)) {
      return true;
    }

    // if the starting and ending point are at the same height, apply new height rules for Hills or mountains
    $sourceHeight = self::getHeight($unit->getPos());
    $targetHeight = self::getHeight($target);
    $cellHeight = self::getHeight($cell);
    if ($targetHeight > 0 && $sourceHeight >= $targetHeight && ($cellHeight <= $sourceHeight || $cellHeight <= $targetHeight)) {
      if ($cellHeight == $sourceHeight || $cellHeight == $targetHeight) {
        $c = self::$grid[$cell['x']][$cell['y']];
        if ($unit->mustSeeToAttack() && !is_null($c['unit']) && $c['unit']->getId() != $unit->getId()) {
          return true;
        }

        foreach ($c['terrains'] as $t) { // usefull for bunker on hill
          if ($t->getHeight() < 1) {
            if ($t->isBlockingLineOfAttack($unit)) {
            return true;
            }

            if ($unit->mustSeeToAttack() && $t->isBlockingLineOfSight($unit, $target, $path)) {
              return true;
            }
          }    
        }
      }
      
      return false;
    }    

    $t = self::$grid[$cell['x']][$cell['y']];
    if ($unit->mustSeeToAttack() && !is_null($t['unit']) && $t['unit']->getId() != $unit->getId()) {
      return true;
    }

    foreach ($t['terrains'] as $t) {
      if ($t->isBlockingLineOfAttack($unit)) {
        return true;
      }

      if ($unit->mustSeeToAttack() && $t->isBlockingLineOfSight($unit, $target, $path)) {
        return true;
      }
    }

    return false;
  }

  // Checking that the path is not blocked with Wadis
  public static function isBlockingWadi($unit, $target, $cell, $path)
  {
    if (!self::isValidCell($cell)) {
      return true;
    }

    $t = self::$grid[$cell['x']][$cell['y']];

    foreach ($t['terrains'] as $t) {
      if (
        $t instanceof \M44\Terrains\Wadi &&
        $unit->mustSeeToAttack() &&
        $t->isBlockingWadi($unit, $target, $path, $cell)
      ) {
        return true;
      }
    }

    $t = self::$grid[$target['x']][$target['y']];
    foreach ($t['terrains'] as $t) {
      if ($t instanceof \M44\Terrains\Wadi && $t->isBlockingWadi($unit, $target, $path, $cell)) {
        return true;
      }
    }

    return false;
  }

  /**
   * Return the list of all the hexagons intersecting the line between the center of source and the center of target
   *  => for each hex, indicate in the "type" field whether the intersection is on one corner, along an edge, or inside the hex
   */
  public static function getCellsInLine($source, $target)
  {
    $cells = [];
    // From the coordinates of the two centers, compute a normal vector
    $sourceCenter = [$source['x'], -3 * $source['y']];
    $targetCenter = [$target['x'], -3 * $target['y']];
    $directorVector = [$targetCenter[0] - $sourceCenter[0], $targetCenter[1] - $sourceCenter[1]];
    $normalVector = [$directorVector[1], -$directorVector[0]];

    // Go through each cells in the "rectangle" bounded by $source and $target
    $minX = min($source['x'], $target['x']);
    $maxX = max($source['x'], $target['x']);
    $offsetX = $minX == $maxX ? 1 : 0;
    $minY = min($source['y'], $target['y']);
    $maxY = max($source['y'], $target['y']);
    $offsetY = $minY == $maxY ? 1 : 0;
    for ($x = $minX - $offsetX; $x <= $maxX + $offsetX; $x++) {
      for ($y = $minY - $offsetY; $y <= $maxY + $offsetY; $y++) {
        if (($x + $y + 4) % 2 == 1) {
          continue;
        }
        $cell = ['x' => $x, 'y' => $y];

        // Compute the center and corners of that cell
        $center = [$x, -3 * $y];
        $corners = [
          [$center[0], $center[1] + 2],
          [$center[0] + 1, $center[1] + 1],
          [$center[0] + 1, $center[1] - 1],
          [$center[0], $center[1] - 2],
          [$center[0] - 1, $center[1] - 1],
          [$center[0] - 1, $center[1] + 1],
        ];

        // Each corner is then sorted in the corresponding category
        $pos = [];
        $neg = [];
        $zeros = [];
        foreach ($corners as $i => $corner) {
          $v = [$corner[0] - $sourceCenter[0], $corner[1] - $sourceCenter[1]];
          $dotProduct = $v[0] * $normalVector[0] + $v[1] * $normalVector[1];
          if ($dotProduct > 0) {
            $pos[] = $i;
          } elseif ($dotProduct < 0) {
            $neg[] = $i;
          } else {
            $zeros[] = $i;
          }
        }

        // Deduce if there is an intersection or not
        if (count($pos) > 0 && count($neg) > 0) {
          $cell['type'] = LINE_INTERSECTION;
        } elseif (count($zeros) == 1) {
          $cell['type'] = empty($pos) ? LINE_TANGENT_LEFT : LINE_TANGENT_RIGHT; // Treat corner the same as border
        } elseif (count($zeros) == 2) {
          $cell['type'] = empty($pos) ? LINE_TANGENT_LEFT : LINE_TANGENT_RIGHT;
        } else {
          continue; // NO INTERSECTION
        }
        $cells[] = $cell;
      }
    }

    return $cells;
  }

  public static function getHeight($pos) {
    $cell = self::$grid[$pos['x']][$pos['y']];
    $cellHeight = 0;
    if (isset($cell['terrains'])) {
      foreach ($cell['terrains'] as $t) {
        $cellHeight = $t->getHeight() > $cellHeight ? $t->getHeight() : $cellHeight;
        }
    }
    return $cellHeight;
  }
  

  /**
   * Return dice number modifier for a given unit and cell for either offense or defense
   */
  public static function getDiceModifier($unit, $cell, $forDefense = true)
  {
    $t = self::$grid[$cell['x']][$cell['y']];
    $m = null;
    foreach ($t['terrains'] as $t) {
      $r = $forDefense ? $t->defense($unit) : $t->offense($unit);
      if (!is_null($r)) {
        $m = is_null($m) ? $r : min($m, $r);
      }
    }

    return $m ?? 0;
  }

  /**
   * Compute the hills components
   */
  public static function getHillComponents()
  {
    if (is_null(self::$hillComponents)) {
      $hills = self::createGrid(false);
      foreach ($hills as $x => $col) {
        foreach ($col as $y => $node) {
          $hills[$x][$y] = self::isHillCell(['x' => $x, 'y' => $y]);
        }
      }

      self::$hillComponents = self::computeConnectedComponents($hills);
    }

    return self::$hillComponents;
  }

  public static function getMountainComponents()
  {
    if (is_null(self::$mountainComponents)) {
      $mountains = self::createGrid(false);
      foreach ($mountains as $x => $col) {
        foreach ($col as $y => $node) {
          $mountains[$x][$y] = self::isMountainCell(['x' => $x, 'y' => $y]);
        }
      }

      self::$mountainComponents = self::computeConnectedComponents($mountains);
    }

    return self::$mountainComponents;
  }

  public static function getCaveComponents()
  {
    if (is_null(self::$caveComponents)) {
      $caves = self::createGrid(false);
      $foundCaves = [];
      foreach ($caves as $x => $col) {
        foreach ($col as $y => $node) {
          if (self::isCaveCell(['x' => $x, 'y' => $y])) {
            $foundCaves[] = ['x' => $x, 'y' => $y];
          }
        }
      }
      self::$caveComponents = $foundCaves;
    }

    return self::$caveComponents;
  }

  /////////////////////////////////////////////
  //  ____      _                  _
  // |  _ \ ___| |_ _ __ ___  __ _| |_
  // | |_) / _ \ __| '__/ _ \/ _` | __|
  // |  _ <  __/ |_| | |  __/ (_| | |_
  // |_| \_\___|\__|_|  \___|\__,_|\__|
  /////////////////////////////////////////////

  /**
   * Compute whether the terrain under a unit allow to reduce 1 flag or not
   */
  public static function canIgnoreOneFlag($unit)
  {
    $cell = $unit->getPos();
    return self::canIgnoreOneFlagCell($unit->getPos(), $unit);
  }

  /**
   * getArgsRetreat: compute the number of hits taken + reachable cells for retreat given min/max number of flags
   */
  public static function getArgsRetreat($unit, $minFlags, $maxFlags)
  {
    // Get all cells accessible at distance at most $maxFlags
    list($cells, $markers) = self::getReachableCellsForRetreat($unit, $maxFlags);
    // Find the maximum number of retreat possible and deduce the number of hits
    $fCells = $cells;
    for ($hits = 0; $minFlags - $hits > 0; $hits++) {
      $fCells = $cells;
      Utils::filter($fCells, function ($cell) use ($minFlags, $maxFlags, $hits) {
        return $cell['d'] <= $maxFlags && $minFlags - $hits <= $cell['d'];
      });

      if (!empty($fCells)) {
        break;
      }
    }

    if ($minFlags == $hits && $hits > 0) {
      // No possible retreat => take hits
      return [
        'hits' => $hits,
        'cells' => [],
      ];
    } else {
      // Keep only cells on a path to these filtered cells
      $closure = [];
      foreach ($fCells as $cell) {
        foreach ($cell['paths'] as $path) {
          foreach ($path['cells'] as $node) {
            if (!in_array($node, $closure)) {
              $closure[] = $node;
            }
          }
        }
      }
      Utils::filterCells($cells, $closure);

      return [
        'hits' => $hits,
        'cells' => $cells,
      ];
    }
  }

  /**
   * getReachableCellsForRetreat: given a distance $d, compute all reeachable cells for $unit at distance <= $d
   */
  public static function getReachableCellsForRetreat($unit, $d)
  {
    // If the terrain is preventing leaving, return empty list
    if (self::cantLeaveCell($unit->getPos(), $unit)) {
      return [[], []];
    }

    // Particular case Train backward move
    //(not necessarly vertical direction but check WAGON retreat instead or opposite LOCOMOTIVE orientation)
    // Case train detected if train case

    $trainCase = in_array($unit->getType(), [LOCOMOTIVE, WAGON]);
    $train = Units::getAll()->filter(function ($unit) {
      return in_array($unit->getType(), [LOCOMOTIVE, WAGON]) && !$unit->isEliminated();
    });

    // Compute all cells reachable at distance $d
    $deltaY = $unit->getCampDirection();

    if ($trainCase) {
      // Compute all cells reachable at distance $d on rails only
      list($cells, $markers) = self::getCellsAtDistance(
        $unit->getPos(),
        $d,

        function ($source, $target, $d) use ($unit, $train) {
          // Check direction to be modified
          /*if ($source['y'] + $deltaY != $target['y']) {
            return \INFINITY;
          }*/

          $targetCell = self::$grid[$target['x']][$target['y']];
          $sourceCell = self::$grid[$source['x']][$source['y']];

          // Train only on railways
          if (!self::isRailCell(['x' => $target['x'], 'y' => $target['y']])) {
            return \INFINITY;
          }

          // Train only can retreat backward direction, case loco only
          if (count($train) == 1) {
            $trainOrientation = $unit->getExtraDatas('orientation');
            $forwardtargets = [];
            $directions = [
              1 => ['x' => -2, 'y' => 0],
              2 => ['x' => -1, 'y' => -1],
              3 => ['x' => 1, 'y' => -1],
              4 => ['x' => 2, 'y' => 0],
              5 => ['x' => 1, 'y' => 1],
              0 => ['x' => -1, 'y' => 1],
            ];
            for ($index = -1; $index <= 1; $index++) {
              $indexDirection = ($trainOrientation + $index + 6) % 6;

              $forwardtargets[$index + 1]['x'] = $source['x'] + $directions[$indexDirection]['x'];
              $forwardtargets[$index + 1]['y'] = $source['y'] + $directions[$indexDirection]['y'];
            }

            if (in_array($target, $forwardtargets)) {
              return \INFINITY;
            }
          }



          // If there is a unit => can't retreat there
          if (!is_null($targetCell['unit'])) {
            return \INFINITY;
          }


          // If the edge is not possible, return infinity
          foreach ($sourceCell['terrains'] as $terrain) {
            if ($terrain->isBlocked($target, $unit)) {
              return INFINITY;
            }
          }
          foreach ($targetCell['terrains'] as $terrain) {
            if ($terrain->isBlocked($source, $unit)) {
              return INFINITY;
            }
          }


          // Otherwise, ask the terrains about it and take the maximum of the costs to check INFINITY
          $cost = 1;
          foreach ($sourceCell['terrains'] as $terrain) {
            $cost = max($cost, $terrain->getLeavingDeplacementCost($unit, $source, $target, $d, false));
          }
          foreach ($targetCell['terrains'] as $terrain) {
            $cost = max($cost, $terrain->getEnteringDeplacementCost($unit, $source, $target, $d, false));
          }

          if ($cost == \INFINITY) {
            return INFINITY;
          }

          // Ignore all other terrains restriction
          return 1;
        },
        function ($cell) use ($unit) {
          return self::avoidIfPossibleCell($cell, $unit) ? 1 : 0;
        }
      );
    } else { // not a train unit
      // Compute all cells reachable at distance $d in the good vertical direction
      list($cells, $markers) = self::getCellsAtDistance(
        $unit->getPos(),
        $d,
        function ($source, $target, $d) use ($unit, $deltaY) {
          // Check direction
          if ($source['y'] + $deltaY != $target['y']) {
            return \INFINITY;
          }

          $targetCell = self::$grid[$target['x']][$target['y']];
          $sourceCell = self::$grid[$source['x']][$source['y']];

          // If there is a unit => can't retreat there
          if (!is_null($targetCell['unit'])) {
            return \INFINITY;
          }

          // If there is an impassable terrain => can't retreat there
          if (self::isImpassableCell($target, $unit) || self::isImpassableForRetreatCell($target, $unit)) {
            return \INFINITY;
          }

          // If the edge is not possible, return infinity
          foreach ($sourceCell['terrains'] as $terrain) {
            if ($terrain->isBlocked($target, $unit)) {
              return INFINITY;
            }
          }
          foreach ($targetCell['terrains'] as $terrain) {
            if ($terrain->isBlocked($source, $unit)) {
              return INFINITY;
            }
          }

          // check to forbid caves teleportation
          if (!in_array(['x' => $target['x'], 'y' => $target['y']], self::getNeighbours($source))) {
            return INFINITY;
          }

          // Otherwise, ask the terrains about it and take the maximum of the costs to check INFINITY
          $cost = 1;
          foreach ($sourceCell['terrains'] as $terrain) {
            $cost = max($cost, $terrain->getLeavingDeplacementCost($unit, $source, $target, $d, false));
          }
          foreach ($targetCell['terrains'] as $terrain) {
            $cost = max($cost, $terrain->getEnteringDeplacementCost($unit, $source, $target, $d, false));
          }

          if ($cost == \INFINITY) {
            return INFINITY;
          }

          // Ignore all other terrains restriction
          return 1;
        },
        function ($cell) use ($unit) {
          return self::avoidIfPossibleCell($cell, $unit) ? 1 : 0;
        }
      );
    }

    return [$cells, $markers];
  }

  /////////////////////////////////////////////
  //   ____      _     _   _   _ _   _ _
  //  / ___|_ __(_) __| | | | | | |_(_) |___
  // | |  _| '__| |/ _` | | | | | __| | / __|
  // | |_| | |  | | (_| | | |_| | |_| | \__ \
  //  \____|_|  |_|\__,_|  \___/ \__|_|_|___/
  ////////////////////////////////////////////

  public static function createGrid($defaultValue = null)
  {
    $mode = Scenario::getMode();
    $dim = self::$dimensions[$mode];
    $g = [];
    for ($y = 0; $y < $dim['y']; $y++) {
      $size = $dim['x'] - ($y % 2 == 0 ? 0 : 1);
      for ($x = 0; $x < $size; $x++) {
        $col = 2 * $x + ($y % 2 == 0 ? 0 : 1);
        $g[$col][$y] = $defaultValue;
      }
    }
    return $g;
  }

  public static function getListOfCells()
  {
    $grid = self::createGrid(0);
    $cells = [];
    foreach ($grid as $x => $col) {
      foreach ($col as $y => $t) {
        $cells[] = ['x' => $x, 'y' => $y];
      }
    }
    return $cells;
  }

  protected static function isValidCell($cell)
  {
    return isset(self::$grid[$cell['x']][$cell['y']]);
  }

  protected static function areSameCell($cell1, $cell2)
  {
    return $cell1['x'] == $cell2['x'] && $cell1['y'] == $cell2['y'];
  }

  public static function getNeighbours($cell, $onlyValidOnes = true)
  {
    $directions = [
      ['x' => -2, 'y' => 0],
      ['x' => -1, 'y' => -1],
      ['x' => 1, 'y' => -1],
      ['x' => 2, 'y' => 0],
      ['x' => 1, 'y' => 1],
      ['x' => -1, 'y' => 1],
    ];

    $cells = [];
    foreach ($directions as $dir) {
      $newCell = [
        'x' => $cell['x'] + $dir['x'],
        'y' => $cell['y'] + $dir['y'],
      ];
      if (self::isValidCell($newCell)) {
        $cells[] = $newCell;
      }
    }
    return $cells;
  }
  /**
   * getNeighnoursStagingArea : get cells close to player's stagingarea
   */
  public static function getNeighboursStagingArea($startingCell, $onlyValidOnes = true) {
    $player = Players::getActive();
    $scenario = Globals::getScenario();
    $mode = Scenario::getMode();
    $sidePlayer1 = isset($scenario['game_info']['side_player1']) ? $scenario['game_info']['side_player1'] : 'AXIS';
    $dim = Board::$dimensions[$mode];
    $yBackLine = $sidePlayer1 == $player->getTeam()->getId() ? 0 : $dim['y']-1;

    $cells = Board::getListOfCells();
    // filter cells on player backline and no unit on cells and not starting cell (current pos of staging area unit)
    $cellsNextStagingArea = array_filter($cells, function ($c) use ($yBackLine, $startingCell) {
      return $c['y'] == $yBackLine 
      && is_null(Board::getUnitInCell($c))
      && $c['x'] != $startingCell['x'];
    });
    // filter cells based on section from activation card
    $card = $player->getCardInPlay();
    $team = $player->getTeam();
    $args = $card->getArgsOrderUnits();
    if (isset($args['sections']) 
    || $card->isType(CARD_INFANTRY_ASSAULT)
    || ($card->isType(CARD_COUNTER_ATTACK) && $card->getExtraDatas('copiedCardType') == \CARD_INFANTRY_ASSAULT)) {
      if (isset($args['sections'])) {
        $argsSections = $args['sections'];
      } elseif ($card->isType(CARD_INFANTRY_ASSAULT)
      || ($card->isType(CARD_COUNTER_ATTACK) && $card->getExtraDatas('copiedCardType') == \CARD_INFANTRY_ASSAULT)) {
        $argsSections = [0, 0, 0];
        $activatedSection = (int) $args['section'];
        $argsSections[$activatedSection] = INFINITY;
      } 
      // filter only starting cells in activated section
      $cellsNextStagingArea2 = array_filter($cellsNextStagingArea, function ($c) use ($argsSections, $team) {
        return $argsSections[self::getCellSections($team->getId(), $c)[0]] != 0;
      });
      return $cellsNextStagingArea2;
    }

    return $cellsNextStagingArea;
  }

  /** 
   * Return an array of 1 or 2 sections of a cell 
   */
  public static function getCellSections($sideId, $cell)
  {
    $mode = Scenario::getMode();
    $flipped = ($sideId == Scenario::getTopTeam());
    $sections = Units::$sections[$mode];
    $sections_results = [];
    for ($i = 0; $i < 3; $i++) {
      if ($sections[$i] <= $cell['x'] && $cell['x'] <= $sections[$i + 1]) {
        $sections_results[] = $flipped ? 2 - $i : $i;
      }
    }
    return $sections_results;
  }

  public static function getCellSubSections($sideId, $cell)
  {
    //OVERLORD BOARD ONLY
    $mode = Scenario::getMode();
    $flipped = ($sideId == Scenario::getTopTeam());
    $sections = Units::$subSections[$mode];
    $sections_results = [];
    for ($i = 0; $i < 6; $i++) {
      if ($sections[$i] <= $cell['x'] && $cell['x'] <= $sections[$i + 1]) {
        $sections_results[] = $flipped ? 2 - $i : $i;
      }
    }
    return $sections_results;
  }


  /**
   * getReachableCellsAtDistance: perform a Disjktra shortest path finding :
   *   - $cell : starting pos
   *   - $d : max distance we are looking for
   *   - $costCallback : function used to compute cost
   */
  protected static function getCellsAtDistance(
    $startingCell,
    $d,
    $costCallback,
    $resistanceCallback = null,
    $computePaths = true,
    $stagingarea = false,
  ) {
    $queue = new \SplPriorityQueue();
    $queue->setExtractFlags(\SplPriorityQueue::EXTR_BOTH);
    $queue->insert(['cell' => $startingCell], 0);
    $markers = self::createGrid(false);
    if ($computePaths) {
      $paths = self::createGrid(false);
      $paths[$startingCell['x']][$startingCell['y']] = [['resistance' => 0, 'cells' => [], 'd' => 0]];
    }

    $stagingareaonce = false;
    while (!$queue->isEmpty()) {
      // Extract the top node and adds it to the result
      $node = $queue->extract();
      if (!$stagingareaonce && $stagingarea) {
        // si l'unité part de staging area, la permiere fois on retire la position courante de l'unité
        $cell = $node['data']['cell'];
        $cell['d'] = -$node['priority'];
        $pos = ['x' => $cell['x'], 'y' => $cell['y']];
      } else {
        $cell = $node['data']['cell'];
        $cell['d'] = -$node['priority'];
        $pos = ['x' => $cell['x'], 'y' => $cell['y']];
        $mark = $markers[$pos['x']][$pos['y']];
        if ($mark !== false) {
         continue;
        }
        $markers[$pos['x']][$pos['y']] = $cell ;
      }


      // Look at neighbours
      if ($stagingarea && !$stagingareaonce) {
        $neighbours = self::getNeighboursStagingArea($startingCell);
        //$stagingareaonce = true;
      } else {
        $neighbours = self::getNeighbours($pos);
      }
      
      foreach ($neighbours as $nextCell) {
        $cost = $stagingarea && $cell['d'] == 0 ? 1 : $costCallback($cell, $nextCell, $d);
        $resistance = is_null($resistanceCallback) || ($stagingarea && $cell['d'] == 0) ? 0 : $resistanceCallback($nextCell);
        $dist = $cell['d'] + $cost;
        $t = $markers[$nextCell['x']][$nextCell['y']];
        if ($t !== false) {
          continue;
        }

        if ($dist <= $d) {
          $nextCell['cost'] = $cost;
          $queue->insert(['cell' => $nextCell], -$dist);

          if ($computePaths) {
            $newPaths = array_map(function ($path) use ($nextCell, $resistance, $cost) {
              return [
                'resistance' => $path['resistance'] + $resistance,
                'cells' => array_merge($path['cells'], [$nextCell]),
                'd' => $path['d'] + $cost,
              ];
            }, $paths[$pos['x']][$pos['y']]);
            Utils::filter($newPaths, function ($path) use ($d) {
              return $path['d'] <= $d;
            });
            $paths[$nextCell['x']][$nextCell['y']] = array_merge(
              $paths[$nextCell['x']][$nextCell['y']] ?: [],
              $newPaths
            );
          }
        }
      }

      if (self::isCaveCell($pos) && $cell['d'] == 0) {
        foreach (self::getCaveComponents() as $cave) {
          if ($cave['x'] == $pos['x'] && $cave['y'] == $pos['y']) {
            continue;
          }
          $cost = $costCallback($cell, $cave, $d);
          $dist = $cell['d'] + $cost;
          $t = $markers[$cave['x']][$cave['y']];
          if ($t !== false) {
            continue;
          }

          if ($dist <= $d) {
            $cave['cost'] = $cost;
            $cave['teleportation'] = in_array(['x' => $cave['x'], 'y' => $cave['y']], self::getNeighbours($startingCell)) ? false : true;
            $queue->insert(['cell' => $cave], -$dist);

            if ($computePaths) {
              $newPaths = array_map(function ($path) use ($cave, $cost) {
                return [
                  'resistance' => $path['resistance'],
                  'cells' => array_merge($path['cells'], [$cave]),
                  'd' => $path['d'] + $cost,
                ];
              }, $paths[$pos['x']][$pos['y']]);
              Utils::filter($newPaths, function ($path) use ($d) {
                return $path['d'] <= $d;
              });
              $paths[$cave['x']][$cave['y']] = array_merge($paths[$cave['x']][$cave['y']] ?: [], $newPaths);
            }
          }
        }
      }
      // if staging area path will have backline cells as firts neighbours
      $stagingareaonce = true;
    }

    // Extract the reachable cells
    $cells = [];
    foreach ($markers as $col) {
      foreach ($col as $cell) {
        if ($cell !== false && $cell['d'] > 0) {
          if ($computePaths) {
            $cell['paths'] = $paths[$cell['x']][$cell['y']];
          }
          $cells[] = $cell;
        }
      }
    }

    return [$cells, $markers];
  }

  /**
   * computeConnectedComponents :
   *  given a bool 2d array for the cells, compute the connected components by assigning each cell a number corresponding to the component
   */
  protected static function computeConnectedComponents($nodes)
  {
    $marks = self::createGrid(0);
    $iComponent = 1;
    foreach ($nodes as $x => $col) {
      foreach ($col as $y => $node) {
        if (!$node || $marks[$x][$y] != 0) {
          continue;
        }

        // If it's a real node and not studied yet => run a DFS on it
        $queue = new \SplQueue();
        $queue->enqueue(['x' => $x, 'y' => $y]);

        while (!$queue->isEmpty()) {
          $cell = $queue->dequeue();
          if ($marks[$cell['x']][$cell['y']] != 0) {
            continue;
          }

          // Mark the component
          $marks[$cell['x']][$cell['y']] = $iComponent;
          // Look at neighbours
          $neighbours = self::getNeighbours($cell);
          foreach ($neighbours as $nextCell) {
            if ($nodes[$nextCell['x']][$nextCell['y']]) {
              $queue->enqueue($nextCell);
            }
          }
        }

        $iComponent++;
      }
    }

    return $marks;
  }

  /**
   * Simulate $n steps of random walk from cell $pos
   */
  public static function randomWalk($pos, $n)
  {
    for ($j = 0; $j < $n; $j++) {
      $neighbours = self::getNeighbours($pos, false);
      $neighbours[] = $pos;
      $key = array_rand($neighbours);
      $pos = $neighbours[$key];
    }

    return self::isValidCell($pos) ? $pos : null;
  }
}
