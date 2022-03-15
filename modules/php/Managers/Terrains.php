<?php
namespace M44\Managers;
use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Helpers\Utils;
use M44\Models\Terrain;
use M44\Board;

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
    $row['tile_id'] = $row['id'];
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
   * getUiData : return all terrain tiles static datas
   */
  public static function getStaticUiData()
  {
    $data = [];
    foreach (TERRAIN_CLASSES as $type => $className) {
      $terrain = self::getInstance($type);
      $data[$terrain->getNumber()] = $terrain->getStaticUiData();
    }
    return $data;
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

  public function add($terrain)
  {
    $terrain['location'] = $terrain['x'] . '_' . $terrain['y'];
    $o = self::singleCreate($terrain);
    Board::addTerrain($o);
    return $o;
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
    $terrains = self::getBackgroundSpecialTerrains($board);
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

  protected function getBackgroundSpecialTerrains($board)
  {
    $terrains = [];

    // Handle beaches
    if ($board['face'] == 'BEACH') {
      foreach (Board::getListOfCells() as $cell) {
        $map = [
          4 => 2,
          5 => 4,
          6 => 6,
          7 => 25,
          8 => 27,
        ];
        $tile = $map[$cell['y']] ?? null;

        if ($tile != null) {
          $tile += ((int) $cell['x'] / 2) % 2;
          $terrains[] = [
            'location' => $cell['x'] . '_' . $cell['y'],
            'tile' => 'background_' . $tile,
            'type' => $tile > 10 ? 'ocean' : 'beach',
            'orientation' => 1,
          ];
        }
      }
    }
    // TODO : handle flooded grounds

    return $terrains;
  }
}
