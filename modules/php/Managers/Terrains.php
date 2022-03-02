<?php
namespace M44\Managers;
use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Helpers\Utils;
use M44\Models\Terrain;

/**
 * Terrains
 */
class Terrains extends \M44\Helpers\Pieces
{
  protected static $table = 'terrains';
  protected static $prefix = 'tile_';
  protected static $customFields = ['type', 'tile', 'orientation', 'extra_datas'];
  protected static $autoreshuffle = false;
  protected static function cast($row)
  {
    $locations = explode('_', $row['location']);
    $row['x'] = $locations[0];
    $row['y'] = $locations[1];
    return self::getInstance($row['type'], $row);
  }

  public function getInstance($type, $row = null)
  {
    if ($type == '') {
      return new Terrain($row);
    } else {
      $className = '\M44\Terrains\\' . TERRAIN_CLASSES[$type];
      return new $className($row);
    }
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

  public static function remove($terrain)
  {
    $terrainId = is_int($terrain) ? $terrain : $terrain->getId();
    self::DB()->delete($terrainId);
  }

  /**
   * Load a scenario
   */
  public function loadScenario($scenario)
  {
    self::DB()
      ->delete()
      ->run();
    $board = $scenario['board'];
    $terrains = [];
    foreach ($board['hexagons'] as $hex) {
      $keys = ['terrain', 'rect_terrain', 'obstacle'];
      foreach ($keys as $key) {
        if (isset($hex[$key])) {
          $terrain = $hex[$key];
          $terrains[] = [
            'location' => $hex['col'] . '_' . $hex['row'],
            'tile' => $terrain['name'],
            'type' => self::getTypeOfTile($terrain),
            'orientation' => $terrain['orientation'] ?? 1,
          ];
        }
      }
    }

    self::create($terrains);
  }

  protected function getTypeOfTile($terrain)
  {
    foreach (TERRAIN_CLASSES as $type => $className) {
      $className = '\M44\Terrains\\' . $className;
      if ($className::isTileOfType($terrain)) {
        return $type;
      }
    }

    return '';
  }
}
