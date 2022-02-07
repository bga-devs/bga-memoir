<?php
namespace M44;
use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Core\Preferences;
use M44\Helpers\Utils;
use M44\Managers\Cards;
use M44\Managers\Players;
use M44\Managers\Terrains;
use M44\Managers\Troops;

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
          'unit' => null,
          'labels' => [],
        ];
      }
    }

    // Add the terrains
    foreach (Terrains::getAllOrdered() as $terrain) {
      self::$grid[$terrain->getX()][$terrain->getY()]['terrains'][] = $terrain;
    }

    // Add the units
    foreach (Troops::getAllOrdered() as $unit) {
      self::$grid[$unit->getX()][$unit->getY()]['unit'] = $unit;
    }

    // Add the labels
    foreach ($scenario['board']['labels'] as $labels) {
      foreach ($labels['text'] as $label) {
        self::$grid[$labels['col']][$labels['row']]['labels'][] = $label;
      }
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

  public static function getReachableCells($troop)
  {
    // Compute remaining moves for the unit
    $m = $troop->getMovementRadius() - $troop->getMoves();
    return self::getReachableCellsAtDistance($troop, $m);
  }

  public static function getReachableCellsAtDistance($troop, $d)
  {
    $queue = new \SplPriorityQueue();
    $queue->setExtractFlags(\SplPriorityQueue::EXTR_BOTH);
    $queue->insert(
      [
        'cell' => $troop->getPos(),
      ],
      0
    );
    $gridMarkers = self::createGrid(false);
    $cells = [];

    while (!$queue->isEmpty()) {
      // Extract the top node and adds it to the result
      $node = $queue->extract();
      $cell = $node['data']['cell'];
      $cell['d'] = $node['priority'];
      if ($gridMarkers[$cell['x']][$cell['y']] !== false) {
        continue;
      }
      $gridMarkers[$cell['x']][$cell['y']] = $cell;
      $cells[] = $cell;

      // Look at neighbours
      foreach (self::getNeighbours($cell) as $nextCell) {
        if ($gridMarkers[$nextCell['x']][$nextCell['y']] !== false) {
          continue;
        }

        $dist = $cell['d'] + self::getDeplacementCost($troop, $cell, $nextCell);
        if ($dist <= $d) {
          $queue->insert(
            [
              'cell' => $nextCell,
            ],
            $dist
          );
        }
      }
    }

    return $cells;
  }

  public static function getDeplacementCost($troop, $source, $target)
  {
    return 1;
  }

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
}
