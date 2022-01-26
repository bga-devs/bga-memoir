<?php
namespace M44\Managers;
use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Helpers\Utils;

/**
 * Terrains
 */
class Terrains extends \M44\Helpers\Pieces
{
  protected static $table = 'terrains';
  protected static $prefix = 'tile_';
  protected static $customFields = ['type', 'orientation', 'extra_datas'];
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
    $className = '\M44\Terrains\\' . TERRAIN_CLASSES[$type];
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
    self::DB()->delete()->run();
    $board = $scenario['board'];
    $terrains = [];
    foreach($board['hexagons'] as $hex){
      $terrain = null;
      if(isset($hex['terrain'])) $terrain = $hex['terrain'];
      if(isset($hex['rect_terrain'])) $terrain = $hex['rect_terrain'];
      if(isset($hex['obstacle'])) $terrain = $hex['obstacle'];
      if($terrain != null){
        $terrains[] = [
          'location' => $hex['col'].'_'.$hex['row'],
          'type' => $terrain['name'],
          'orientation' => $terrain['orientation'] ?? 1,
        ];
      }
    }

    self::create($terrains);
  }
}