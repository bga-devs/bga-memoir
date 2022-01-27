<?php
namespace M44\Managers;
use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Helpers\Utils;

/**
 * Troops
 */
class Troops extends \M44\Helpers\Pieces
{
  protected static $table = 'troops';
  protected static $prefix = 'troop_';
  protected static $customFields = ['type', 'nation', 'figures', 'badge', 'extra_datas'];
  protected static $autoreshuffle = false;
  protected static function cast($row)
  {
    $locations = explode('_', $row['location']);
    $row['x'] = $locations[0];
    $row['y'] = $locations[1];
    return self::getInstance($row['type'], $row['badge'], $row);
  }

  public function getInstance($type, $badge, $row = null)
  {
    $className = '\M44\Troops\\' . TROOP_CLASSES[$type];
    return new $className($row);
  }

  //////////////////////////////////
  //////////////////////////////////
  //////////// GETTERS /////////////
  //////////////////////////////////
  //////////////////////////////////

  /**
   * getUiData : return all terrain tiles
   */
  public static function getUiData()
  {
    return self::getAllOrdered()->ui();
  }

  //////////////////////////////////
  //////////////////////////////////
  ///////////// SETTERS //////////////
  //////////////////////////////////
  //////////////////////////////////

  /**
   * Load a scenario
   */
  public function loadScenario($scenario)
  {
    self::DB()
      ->delete()
      ->run();
    $board = $scenario['board'];
    $troops = [];
    foreach ($board['hexagons'] as $hex) {
      if (isset($hex['unit'])) {
        $data = self::getTypeAndNation($hex['unit']);
        $troop = self::getInstance($data['type'], $data['badge']);
        $data['figures'] = $troop->getMaxUnits();
        $data['location'] = $hex['col'] . '_' . $hex['row'];
        $troops[] = $data;
      }
    }

    self::create($troops);
  }

  ////////////////////////
  //////// Utils /////////
  ////////////////////////
  public function getTypeAndNation($unit)
  {
    $name = $unit['name'];
    foreach (array_keys(TROOP_CLASSES) as $t) {
      if (stripos($name, $t) !== false) {
        $nation = substr($name, strlen($t));
        return [
          'type' => $t,
          'nation' => $nation,
          'badge' => $unit['badge'] ?? null,
        ];
      }
    }

    return null;
  }
}
