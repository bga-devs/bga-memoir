<?php
namespace M44;
use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Core\Preferences;
use M44\Helpers\Utils;
use M44\Managers\Cards;
use M44\Managers\Players;
use M44\Managers\Terrains;
use M44\Managers\Units;

const LINE_INTERSECTION = 0;
const LINE_TANGENT = 1;
const LINE_CORNER = 2;

class Board extends \APP_DbObject
{
  public static $dimensions = [
    STANDARD_DECK => ['x' => 13, 'y' => 9],
    BREAKTHROUGH_DECK => ['x' => 13, 'y' => 17],
    OVERLORD_DECK => ['x' => 26, 'y' => 9],
  ];

  protected static $grid = [];
  protected static $scenario = null;
  public function getScenario()
  {
    if (self::$scenario == null) {
      $scenario = self::getUniqueValueFromDB("SELECT value FROM global_variables WHERE name = 'scenario' LIMIT 1");
      self::$scenario = is_null($scenario) ? null : json_decode($scenario, true);
    }
    return self::$scenario;
  }

  public function getMode()
  {
    $scenario = self::getScenario();
    return is_null($scenario) ? null : $scenario['board']['type'];
  }

  public function init()
  {
    // Try to fetch scenario from DB
    $scenario = self::getScenario();
    if (is_null($scenario)) {
      return;
    }

    // Create the board
    self::$grid = self::createGrid();
    foreach (self::$grid as $x => $col) {
      foreach ($col as $y => $cell) {
        self::$grid[$x][$y] = [
          'terrains' => [],
          'units' => [],
          'labels' => [],
        ];
      }
    }

    // Add the terrains
    foreach (Terrains::getAllOrdered() as $terrain) {
      self::$grid[$terrain->getX()][$terrain->getY()]['terrains'][] = $terrain;
    }

    // Add the units
    foreach (Units::getAllOrdered() as $unit) {
      self::$grid[$unit->getX()][$unit->getY()]['units'][] = $unit;
    }

    // Add the labels
    foreach ($scenario['board']['labels'] as $labels) {
      foreach ($labels['text'] as $label) {
        self::$grid[$labels['col']][$labels['row']]['labels'][] = $label;
      }
    }
  }

  public function refreshUnits()
  {
    foreach (self::$grid as $x => $col) {
      foreach ($col as $y => $cell) {
        self::$grid[$x][$y]['units'] = [];
      }
    }
    foreach (Units::getAllOrdered() as $unit) {
      self::$grid[$unit->getX()][$unit->getY()]['units'][] = $unit;
    }
  }

  public function getUiData()
  {
    $scenario = self::getScenario();
    if (is_null($scenario)) {
      return null;
    }
    return [
      'type' => $scenario['board']['type'],
      'face' => $scenario['board']['face'],
      'grid' => self::$grid,
    ];
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
    // Compute remaining moves for the unit
    $m = $unit->getMovementRadius() - $unit->getMoves();
    return self::getReachableCellsAtDistance($unit, $m);
  }

  public static function getReachableCellsAtDistance($unit, $d, $unitaryCost = false)
  {
    $queue = new \SplPriorityQueue();
    $queue->setExtractFlags(\SplPriorityQueue::EXTR_BOTH);
    $queue->insert(
      [
        'cell' => $unit->getPos(),
      ],
      0
    );
    $gridMarkers = self::createGrid(false);
    $cells = [];

    while (!$queue->isEmpty()) {
      // Extract the top node and adds it to the result
      $node = $queue->extract();
      $cell = $node['data']['cell'];
      $cell['d'] = -$node['priority'];
      if ($gridMarkers[$cell['x']][$cell['y']] !== false) {
        continue;
      }
      $gridMarkers[$cell['x']][$cell['y']] = $cell;
      if ($cell['d'] > 0) {
        $cells[] = $cell;
      }

      // Look at neighbours
      $neighbours = self::getNeighbours($cell);
      foreach ($neighbours as $nextCell) {
        if ($gridMarkers[$nextCell['x']][$nextCell['y']] !== false) {
          continue;
        }

        $dist = $cell['d'] + ($unitaryCost ? 1 : self::getDeplacementCost($unit, $cell, $nextCell, $d));
        if ($dist <= $d) {
          $queue->insert(
            [
              'cell' => $nextCell,
            ],
            -$dist
          );
        }
      }
    }
    //echo '<pre>'; var_dump($gridMarkers); echo '</pre>';

    return $cells;
  }

  public static function getDeplacementCost($unit, $source, $target, $d)
  {
    // Get corresponding cells
    $sourceCell = self::$grid[$source['x']][$source['y']];
    $targetCell = self::$grid[$target['x']][$target['y']];

    // If there is a unit => can't go there
    if (!empty($targetCell['units'])) {
      return \INFINITY;
    }

    // If there is an impassable terrain => can't go there
    foreach ($targetCell['terrains'] as $terrain) {
      $impassable = $terrain->getImpassable();
      if ($impassable === true || (is_array($impassable) && in_array($unit->getType(), $impassable))) {
        return \INFINITY;
      }
    }

    // If I'm coming from a 'must stop' terrain, can't go there unless dist = 0
    if ($source['d'] > 0) {
      foreach ($sourceCell['terrains'] as $terrain) {
        if ($terrain->mustStopWhenEntering()) {
          return \INFINITY;
        }
      }
    }

    return 1;
  }

  //////////////////////////////////////////
  //    _  _____ _____  _    ____ _  __
  //    / \|_   _|_   _|/ \  / ___| |/ /
  //   / _ \ | |   | | / _ \| |   | ' /
  //  / ___ \| |   | |/ ___ \ |___| . \
  // /_/   \_\_|   |_/_/   \_\____|_|\_\
  //////////////////////////////////////////

  public static function getTargetableCells($unit)
  {
    // Compute cells at distance
    $m = 10; //count($unit->getAttackPower());
    $cells = self::getReachableCellsAtDistance($unit, $m, true);

    // Keep only the ones where an enemy stands
    Utils::filter($cells, function ($cell) use ($unit) {
      $units = self::$grid[$cell['x']][$cell['y']]['units'];
      Utils::filter($units, function ($unit2) use ($unit) {
        return $unit2->isOpponent($unit);
      });
      return !empty($units);
    });

    foreach ($cells as &$cell) {
      $cell['path'] = self::getCellsInLine($unit->getPos(), $cell);
    }

    return $cells;
  }

  public static function isInLineOfSight($source, $target)
  {
  }

  public static function getCellsInLine($source, $target)
  {
    $cells = [];
    // From the coordinates of the two centers, compute a normal vector
    $sourceCenter = [2 * $source['x'], 4 * $source['y']];
    $targetCenter = [2 * $target['x'], 4 * $target['y']];
    $normalVector = [$targetCenter[1] - $sourceCenter[1], -($targetCenter[0] - $sourceCenter[0])];

    // Go through each cells in the "rectangle" bounded by $source and $target
    for ($x = min($source['x'], $target['x']); $x <= max($source['x'], $target['x']); $x++) {
      for ($y = min($source['y'], $target['y']); $y <= max($source['y'], $target['y']); $y++) {
        $cell = ['x' => $x, 'y' => $y];
        if (!self::isValidCell($cell)) {
          continue;
        }

        // Compute the center and corners of that cell
        $center = [2 * $x, 4 * $y];
        $corners = [
          [$center[0], $center[1] - 2],
          [$center[0] + 1, $center[1] - 1],
          [$center[0] + 1, $center[1] + 1],
          [$center[0], $center[1] + 2],
          [$center[0] - 1, $center[1] + 1],
          [$center[0] - 1, $center[1] - 1],
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
          $cell['type'] = LINE_TANGENT;
        } else {
          continue; // NO INTERSECTION
        }
        $cells[] = $cell;
      }
    }

    return $cells;
  }

  /////////////////////////////////////////////
  //  ____      _     _   _   _ _   _ _
  //  / ___|_ __(_) __| | | | | | |_(_) |___
  // | |  _| '__| |/ _` | | | | | __| | / __|
  // | |_| | |  | | (_| | | |_| | |_| | \__ \
  //  \____|_|  |_|\__,_|  \___/ \__|_|_|___/
  ////////////////////////////////////////////

  public static function createGrid($defaultValue = null)
  {
    $mode = self::getMode();
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
}
