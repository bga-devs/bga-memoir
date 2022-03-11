<?php
namespace M44;
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
  public function init()
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
          'medals' => [],
        ];
      }
    }

    // Add the terrains
    foreach (Terrains::getAllOrdered() as $terrain) {
      self::$grid[$terrain->getX()][$terrain->getY()]['terrains'][] = $terrain;
    }

    // Add the units
    foreach (Units::getAllOrdered() as $unit) {
      if ($unit->getNUnits() > 0) {
        self::$grid[$unit->getX()][$unit->getY()]['unit'] = $unit;
      }
    }

    // Add the labels
    foreach ($scenario['board']['labels'] as $labels) {
      foreach ($labels['text'] as $label) {
        self::$grid[$labels['col']][$labels['row']]['labels'][] = $label;
      }
    }

    // Add the medals
    foreach (Medals::getOnBoard() as $medal) {
      self::$grid[$medal['x']][$medal['y']]['medals'][] = $medal;
    }
  }

  public function removeTerrain($terrain)
  {
    $x = $terrain->getX();
    $y = $terrain->getY();
    Utils::filter(self::$grid[$x][$y]['terrains'], function ($t) use ($terrain) {
      return $t->getId() != $terrain->getId();
    });
    Terrains::remove($terrain);
  }

  public function addTerrain($terrain)
  {
    self::$grid[$terrain->getX()][$terrain->getY()]['terrains'][] = $terrain;
  }

  public function removeUnit($unit)
  {
    self::$grid[$unit->getX()][$unit->getY()]['unit'] = null;

    // Check for listeners
    foreach (self::$grid[$unit->getX()][$unit->getY()]['terrains'] as $terrain) {
      $terrain->onUnitEliminated($unit);
    }

    // Check for potential lost medals
    Medals::checkBoardMedals();

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

  public function getUiData()
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

  public function getUnitInCell($x, $y = null)
  {
    if ($y === null) {
      $y = $x['y'];
      $x = $x['x'];
    }
    return self::$grid[$x][$y]['unit'];
  }

  public function getTerrainsInCell($x, $y = null)
  {
    if ($y === null) {
      $y = $x['y'];
      $x = $x['x'];
    }
    return self::$grid[$x][$y]['terrains'];
  }

  // Useful for close assault card
  public function isAdjacentToEnnemy($unit)
  {
    foreach (self::getNeighbours($unit->getPos()) as $cell) {
      $t = self::$grid[$cell['x']][$cell['y']];
      if ($t['unit'] !== null && $t['unit']->isOpponent($unit)) {
        return true;
      }
    }

    return false;
  }

  /**
   * Do a generic OR on a given property of all terrains on the cell
   */
  public static function cellHasProperty($cell, $property, $unit)
  {
    $t = self::$grid[$cell['x']][$cell['y']];
    foreach ($t['terrains'] as $terrain) {
      if ($terrain->$property($unit)) {
        return true;
      }
    }
    return false;
  }

  public static function mustStopWhenEntering($unit, $cell)
  {
    return self::cellHasProperty($cell, 'mustStopWhenEntering', $unit);
  }

  public static function mustBeAdjacentToEnter($unit, $cell)
  {
    return self::cellHasProperty($cell, 'mustBeAdjacentToEnter', $unit);
  }

  // Useful for DigIn card
  public function canPlaceSandbag($unit)
  {
    return self::cellHasProperty($unit->getPos(), 'isBlockingSandbag', $unit);
  }

  /////////////////////////////////
  //  __  __  _____     _______
  // |  \/  |/ _ \ \   / / ____|
  // | |\/| | | | \ \ / /|  _|
  // | |  | | |_| |\ V / | |___
  // |_|  |_|\___/  \_/  |_____|
  /////////////////////////////////

  public static function getReachableCells($unit)
  {
    // Already moved before ?
    $uId = Globals::getUnitMoved();
    if ($unit->getMoves() > 0 && $uId != -1 && $uId != $unit->getId()) {
      return [];
    }

    // Compute remaining moves for the unit
    $maxDistance = $unit->getMovementRadius();
    if ($unit->getActivationOCard()->getType() == \CARD_BEHIND_LINES) {
      $maxDistance = 3; // Units activated by "BehindEnemyLines" can moves up to 3 hexes
    }
    $m = $maxDistance - $unit->getMoves();
    return self::getReachableCellsAtDistance($unit, $m);
  }

  /**
   * getReachableCellsAtDistance: find all the cells reachable for movements
   *   - $unit : a Unit object, used to compute starting pos and movement costs
   *   - $d : max distance we are looking for
   */
  public static function getReachableCellsAtDistance($unit, $d)
  {
    $startingCell = $unit->getPos();
    list($cells, $markers) = self::getCellsAtDistance($startingCell, $d, function ($source, $target, $d) use ($unit) {
      return self::getDeplacementCost($unit, $source, $target, $d);
    });

    // Compute for each cell whether the unit might be able to attack after the move
    foreach ($cells as &$cell) {
      if (!empty(self::getTargetableCells($unit, $cell, $cell['d']))) {
        $cell['canAttack'] = true;
      }
    }

    return $cells;
  }

  public static function getDeplacementCost($unit, $source, $target, $d)
  {
    // Get corresponding cells
    $sourceCell = self::$grid[$source['x']][$source['y']];
    $targetCell = self::$grid[$target['x']][$target['y']];

    // If there is a unit => can't go there
    if (!is_null($targetCell['unit'])) {
      return \INFINITY;
    }

    // If there is an impassable terrain => can't go there
    foreach ($targetCell['terrains'] as $terrain) {
      if ($terrain->isImpassable($unit)) {
        return \INFINITY;
      }
    }

    // Units activated by "BehindEnemyLines" card have no terrain restriction
    if ($unit->getActivationOCard()->getType() == \CARD_BEHIND_LINES) {
      return 1;
    }

    // If I'm coming from a 'must stop' terrain, can't go there unless dist = 0
    if ($source['d'] > 0 || $unit->getMoves() > 0) {
      if (self::mustStopWhenEntering($unit, $source)) {
        return \INFINITY;
      }
    }

    // If I'm going to a 'must be adjacent' terrain, can't go there unless dist = 0
    if ($source['d'] > 0 || $unit->getMoves() > 0) {
      if (self::mustBeAdjacentToEnter($unit, $target)) {
        return \INFINITY;
      }
    }

    return 1;
  }

  public static function moveUnit($unit, $cell, $isRetreat = false)
  {
    $pos = $unit->getPos();
    $unit->moveTo($cell);
    self::$grid[$pos['x']][$pos['y']]['unit'] = null;
    self::$grid[$cell['x']][$cell['y']]['unit'] = $unit;

    // Check listener
    $sourceCell = self::$grid[$pos['x']][$pos['y']];
    $targetCell = self::$grid[$cell['x']][$cell['y']];
    foreach ($sourceCell['terrains'] as $terrain) {
      $terrain->onUnitLeaving($unit, $isRetreat);
    }
    foreach ($targetCell['terrains'] as $terrain) {
      $terrain->onUnitEntering($unit, $isRetreat);
    }

    Medals::checkBoardMedals();
    if (Teams::checkVictory()) {
      return true;
    }
    return false;
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
    // Already attacked before ?
    $uId = Globals::getUnitAttacker();
    if ($unit->getFights() > 0 && $uId != -1 && $uId != $unit->getId()) {
      return [];
    }

    // Check whether the unit moved too much to attack
    $m = $unit->getMoves() + ($moves ?? 0);
    if ($m > $unit->getMovementAndAttackRadius() && $unit->getActivationOCard()->getType() != \CARD_BEHIND_LINES) {
      return [];
    }

    // Check whether unit moved into a cell that prevent attack
    $pos = $cell ?? $unit->getPos();
    if ($m > 0) {
      foreach (self::getTerrainsInCell($pos) as $terrain) {
        if ($terrain->enteringCannotBattle($unit)) {
          return [];
        }
      }
    }

    // Compute cells at fire range
    $power = $unit->getAttackPower();
    $range = count($power);
    list($cells, $markers) = self::getCellsAtDistance($pos, $range, function ($source, $target, $d) {
      return 1;
    });

    // Keep only the ones where an enemy stands
    Utils::filter($cells, function ($cell) use ($unit) {
      $oppUnit = self::getUnitInCell($cell);
      return !is_null($oppUnit) && $oppUnit->isOpponent($unit);
    });

    // Keep only the cells in sight if unit need to see to shoot
    if ($unit->mustSeeToAttack()) {
      Utils::filter($cells, function ($cell) use ($unit, $pos) {
        return self::isInLineOfSight($unit, $cell, $pos);
      });
    }

    // Compute the opponents in contact with the unit
    $inContact = array_values(
      \array_filter($cells, function ($cell) {
        return $cell['d'] == 1;
      })
    );
    if (!empty($inContact)) {
      $cells = $inContact; // If at least one in contact => must fight one of them
    }

    // Compute shooting powers for the remaining cells
    foreach ($cells as &$cell) {
      $cell['dice'] = $power[$cell['d'] - 1];
      $offenseModifier = self::getDiceModifier($unit, $pos, false);
      $defenseModifier = self::getDiceModifier($unit, $cell, true);
      $cardModifier = 0;
      if ($unit->getActivationOCard() != null) {
        $cardModifier = $unit->getActivationOCard()->getDiceModifier($unit, $cell);
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
    foreach ($path as $cell) {
      // Starting and ending points are never blocking
      if (self::areSameCell($cell, $source) || self::areSameCell($cell, $target)) {
        continue;
      }

      // If the cell is not blocking the line of sight, skip to the next one
      if (!self::isBlockingLineOfSight($unit, $cell)) {
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
  public static function isBlockingLineOfSight($unit, $cell)
  {
    $t = self::$grid[$cell['x']][$cell['y']];
    if (!is_null($t['unit']) && $t['unit']->getId() != $unit->getId()) {
      return true;
    }

    foreach ($t['terrains'] as $t) {
      if ($t->isBlockingLineOfSight($unit)) {
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
        $cell = ['x' => $x, 'y' => $y];
        if (!self::isValidCell($cell)) {
          continue;
        }

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
          $cell['type'] = LINE_CORNER;
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
    $t = self::$grid[$cell['x']][$cell['y']];
    foreach ($t['terrains'] as $t) {
      if ($t->canIgnoreOneFlag($unit)) {
        return true;
      }
    }

    return false;
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
          foreach ($path as $node) {
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
    // Compute all cells reachable at distance $d in the good vertical direction
    $deltaY = $unit->getCampDirection();
    list($cells, $markers) = self::getCellsAtDistance($unit->getPos(), $d, function ($source, $target, $d) use (
      $unit,
      $deltaY
    ) {
      // Check direction
      if ($source['y'] + $deltaY != $target['y']) {
        return \INFINITY;
      }

      $targetCell = self::$grid[$target['x']][$target['y']];
      // If there is a unit => can't retreat there
      if (!is_null($targetCell['unit'])) {
        return \INFINITY;
      }

      // If there is an impassable terrain => can't retreat there
      foreach ($targetCell['terrains'] as $terrain) {
        if ($terrain->isImpassable($unit)) {
          return \INFINITY;
        }
      }

      // Ignore all other terrains restriction
      return 1;
    });

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

  protected function isValidCell($cell)
  {
    return isset(self::$grid[$cell['x']][$cell['y']]);
  }

  protected function areSameCell($cell1, $cell2)
  {
    return $cell1['x'] == $cell2['x'] && $cell1['y'] == $cell2['y'];
  }

  protected function getNeighbours($cell)
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
   * getReachableCellsAtDistance: perform a Disjktra shortest path finding :
   *   - $cell : starting pos
   *   - $d : max distance we are looking for
   *   - $costCallback : function used to compute cost
   */
  protected static function getCellsAtDistance($startingCell, $d, $costCallback)
  {
    $queue = new \SplPriorityQueue();
    $queue->setExtractFlags(\SplPriorityQueue::EXTR_BOTH);
    $queue->insert(
      [
        'cell' => $startingCell,
        'paths' => [[]],
      ],
      0
    );
    $markers = self::createGrid(false);

    while (!$queue->isEmpty()) {
      // Extract the top node and adds it to the result
      $node = $queue->extract();
      $cell = $node['data']['cell'];
      $cell['d'] = -$node['priority'];
      $pos = ['x' => $cell['x'], 'y' => $cell['y']];
      $mark = $markers[$pos['x']][$pos['y']];
      if ($mark !== false) {
        if ($mark['d'] == $cell['d']) {
          $markers[$pos['x']][$pos['y']]['paths'] = array_merge($mark['paths'], $node['data']['paths']);
        }
        continue;
      }
      $cell['paths'] = $node['data']['paths'];
      $markers[$pos['x']][$pos['y']] = $cell;

      // Look at neighbours
      $neighbours = self::getNeighbours($pos);
      foreach ($neighbours as $nextCell) {
        $dist = $cell['d'] + $costCallback($cell, $nextCell, $d);
        $t = $markers[$nextCell['x']][$nextCell['y']];
        if ($t !== false) {
          continue;
        }

        if ($dist <= $d) {
          $queue->insert(
            [
              'cell' => $nextCell,
              'paths' => array_map(function ($path) use ($nextCell) {
                return array_merge($path, [$nextCell]);
              }, $markers[$pos['x']][$pos['y']]['paths']),
            ],
            -$dist
          );
        }
      }
    }

    // Extract the reachable cells
    $cells = [];
    foreach ($markers as $col) {
      foreach ($col as $cell) {
        if ($cell !== false && $cell['d'] > 0) {
          $cells[] = $cell;
        }
      }
    }

    return [$cells, $markers];
  }
}
