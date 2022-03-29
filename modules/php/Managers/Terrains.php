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
  protected static $customFields = ['type', 'tile', 'orientation', 'owner', 'extra_datas'];
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
    $options = $scenario['game_info']['options'] ?? [];

    foreach ($board['hexagons'] as $hex) {
      $keys = ['terrain', 'rect_terrain', 'obstacle'];
      foreach ($keys as $key) {
        if (isset($hex[$key])) {
          $terrain = $hex[$key];
          $type = self::getTypeOfTile($terrain);
          if ($type == '') {
            throw new \BgaVisibleSystemException('Unsupported terrains' . \var_export($terrain, true));
          }

          // Fallback code for bunker without original_owner flag
          if ($type == 'bunker' && !isset($terrain['original_owner'])) {
            $data = Units::getTypeAndNation($hex['unit']);
            $terrain['original_owner'] = in_array($data['nation'], Units::$nations[ALLIES]) ? ALLIES : AXIS;
          }

          // Custom properties
          $properties = $terrain['properties'] ?? [];
          $behavior = $terrain['behavior'] ?? null;
          if ($behavior == 'IMPASSABLE_HILL') {
            $properties['isImpassable'] = true;
          } elseif ($behavior == 'IMPASSABLE_BLOCKING_HILL') {
            $properties['isImpassable'] = true;
            $properties['isBlockingLineOfSight'] = true;
            $properties['isBlockingLineOfAttack'] = true;
          } elseif ($behavior == 'BRIDGE_SECTION') {
            $properties['bridgeSection'] = true;
          } elseif ($behavior == 'WIDE_RIVER') {
            $properties['isBlockingLineOfSight'] = [\INFANTRY];
          } elseif ($behavior == 'OASIS_RECOVERY') {
            $properties['canRecover'] = true;
          }

          if (isset($options['hill317'])) {
            $coords = Utils::computeCoords(['x' => $hex['col'], 'y' => $hex['row']]);
            if (in_array($coords, array_values($options['hill317']))) {
              $properties['hill317'] = true;
            }
          }
          // Extra data
          $extraDatas = empty($properties)
            ? null
            : [
              'properties' => $properties,
            ];

          $terrains[] = [
            'location' => $hex['col'] . '_' . $hex['row'],
            'tile' => $terrain['name'],
            'type' => $type,
            'orientation' => $terrain['orientation'] ?? 1,
            'owner' => $terrain['original_owner'] ?? null,
            'extra_datas' => $extraDatas,
          ];
        }
      }
    }

    // Handle mines
    $mineTokens = [0, 0, 0, 1, 1, 2, 2, 3, 4];
    foreach ($board['hexagons'] as $hex) {
      foreach ($hex['tags'] ?? [] as $tag) {
        if (($tag['behavior'] ?? null) == 'MINE_FIELD') {
          shuffle($mineTokens);
          $value = \array_pop($mineTokens);
          $terrains[] = [
            'location' => $hex['col'] . '_' . $hex['row'],
            'tile' => 'mineX',
            'type' => 'minefield',
            'orientation' => 1,
            'owner' => $tag['side'] ?? null,
            'extra_datas' => json_encode([
              'value' => $value,
            ]),
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

    // Remove the beach/flooded hexes if something else is on top of it
    $locations = [];
    foreach ($board['hexagons'] as $hex) {
      if (isset($hex['terrain'])) {
        $locations[] = $hex['col'] . '_' . $hex['row'];
      }
    }
    Utils::filter($terrains, function ($terrain) use ($locations) {
      return !in_array($terrain['location'], $locations);
    });

    return $terrains;
  }
}
