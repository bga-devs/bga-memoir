<?php
namespace M44;
use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Core\Preferences;
use M44\Helpers\Utils;
use M44\Managers\Cards;
use M44\Managers\Players;
use M44\Managers\Terrains;

class Board extends \APP_DbObject
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
    $scenario = self::getUniqueValueFromDB("SELECT value FROM global_variables WHERE name = 'scenario' LIMIT 1");
    if (is_null($scenario)) {
      return;
    }
    $scenario = json_decode($scenario, true);

    // Create the board
    self::$grid = [];
    $dim = self::$dimensions[$scenario['board']['type']];
    for ($y = 0; $y < $dim['y']; $y++) {
      $size = $dim['x'] - ($y % 2 == 0 ? 0 : 1);
      for ($x = 0; $x < $size; $x++) {
        $col = 2 * $x + ($y % 2 == 0 ? 0 : 1);
        self::$grid[$col][$y] = [
          'terrains' => [],
          'units' => [],
        ];
      }
    }

    // Add the terrains
    foreach (Terrains::getAllOrdered() as $terrain) {
      self::$grid[$terrain->getX()][$terrain->getY()]['terrains'][] = $terrain;
    }
  }

  protected function isValidCell($cell)
  {
    return isset(self::$grid[$cell['x']][$cell['y']]);
  }
}
