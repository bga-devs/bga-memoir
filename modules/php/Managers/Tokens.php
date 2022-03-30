<?php
namespace M44\Managers;
use M44\Core\Game;
use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Core\Preferences;
use M44\Helpers\Collection;
use M44\Managers\Units;
use M44\Board;
use M44\Helpers\Utils;
use M44\Managers\Medals;

/*
 * Medals manager
 */
class Tokens extends \M44\Helpers\Pieces
{
  protected static $table = 'tokens';
  protected static $prefix = 'token_';
  protected static $customFields = ['type', 'team', 'x', 'y', 'sprite', 'datas'];
  protected static $autoreshuffle = false;
  protected static function cast($row)
  {
    if (isset($row['datas'])) {
      $row['datas'] = \json_decode($row['datas'], true);
    }

    return $row;
  }

  public function getOfTeam($team)
  {
    return self::DB()
      ->where('team', $team)
      ->get();
  }

  public static function addCoordsClause(&$q, $coords)
  {
    $q = $q->where('x', $coords['x'])->where('y', $coords['y']);
    return $q;
  }

  public static function addTypeClause(&$q, $type)
  {
    $q = $q->where('type', $type);
    return $q;
  }

  public function getOnCoords($location, $coords, $type = null)
  {
    $query = self::getSelectWhere(null, $location, null);
    if ($coords != null) {
      self::addCoordsClause($query, $coords);
    }
    if ($type != null) {
      self::addTypeClause($query, $type);
    }
    return $query->get();
  }

  /**
   * Load a scenario
   */
  public function loadScenario($scenario, $rematch)
  {
    self::DB()
      ->delete()
      ->run();

    $board = $scenario['board'];
    $tokens = [];
    $exitMedals = $scenario['game_info']['options']['infantry_exit_counts_2'] ?? false ? 2 : 1;

    foreach ($board['hexagons'] as $hex) {
      $tags = $hex['tags'] ?? [];
      foreach ($tags as $tag) {
        $baseDatas = [
          'location' => 'board',
          'x' => $hex['col'],
          'y' => $hex['row'],
          'sprite' => $tag['name'],
        ];

        // Medal
        if (strpos($tag['name'], 'medal') === 0) {
          $tokens[] = self::extractMedalDatas($hex, $tag, $baseDatas);
        }
        // Mines
        elseif (in_array($tag['behavior'] ?? null, ['MINE_FIELD'])) {
          continue; // Handle in terrains instead
        }
        // Camouflage tags
        elseif (in_array($tag['name'], ['tag14', 'tag15'])) {
          $baseDatas['type'] = TOKEN_CAMOUFLAGE;
          $tokens[] = $baseDatas;
        } elseif (($tag['behavior'] ?? null) == 'EXIT_MARKER') {
          // at least the hex on the marker.
          $tokens[] = [
            'location' => 'board',
            'x' => $hex['col'],
            'y' => $hex['row'],
            'sprite' => $tag['name'],
            'type' => \TOKEN_EXIT_MARKER,
            'team' => $tag['side'] ?? null,
            'datas' => json_encode([
              'medals' => $exitMedals,
            ]),
          ];
          $origin = ['x' => $hex['col'], 'y' => $hex['row']];

          foreach ($tag['group'] as $g) {
            $calc = Utils::revertCoords($g);
            if ($calc == $origin) {
              continue;
            }
            // throw new \feException(print_r($calc));
            $tokens[] = [
              'location' => 'board',
              'x' => $calc['x'],
              'y' => $calc['y'],
              'sprite' => $tag['name'],
              'type' => \TOKEN_EXIT_MARKER,
              'team' => $tag['side'] ?? null,
              'datas' => json_encode([
                'medals' => $exitMedals,
              ]),
            ];
          }
        }
      }
    }

    // Add sudden death tokens
    if (isset($scenario['game_info']['victory']) && isset($scenario['game_info']['victory']['condition'])) {
      $condition = $scenario['game_info']['victory']['condition'];
      if (isset($condition[0]['group_sudden_death'])) {
        // TODO : Not very robust, can we have an array of condition ??
        $tokens[] = self::extractSuddenDeathDatas($condition[0]['group_sudden_death']);
      }
    }

    if (!empty($tokens)) {
      self::create($tokens);
    }
  }

  //////////////////////////////////////
  //  __  __          _       _
  // |  \/  | ___  __| | __ _| |___
  // | |\/| |/ _ \/ _` |/ _` | / __|
  // | |  | |  __/ (_| | (_| | \__ \
  // |_|  |_|\___|\__,_|\__,_|_|___/
  //////////////////////////////////////

  /**
   * extract all the needed datas from m44 about the medal
   */
  protected function extractMedalDatas($hex, $tag, $baseDatas)
  {
    $team = in_array($tag['name'], ['medal1', 'medal4', 'medal6']) ? ALLIES : AXIS;
    $permanent = $tag['medal']['permanent'] ?? false;
    $hexes = [['x' => $hex['col'], 'y' => $hex['row']]];
    if (isset($tag['group']) && !empty($tag['group'])) {
      foreach ($tag['group'] as $g) {
        $hexes[] = Utils::revertCoords($g);
      }
    }

    $baseDatas['type'] = \TOKEN_MEDAL;
    $baseDatas['team'] = $team;
    $baseDatas['datas'] = json_encode([
      'permanent' => $permanent ? 1 : 0,
      'counts_for' => $tag['medal']['counts_for'] ?? 1,
      'nbr_hex' => $tag['medal']['nbr_hex'] ?? 1,
      'group' => $hexes,
    ]);

    return $baseDatas;
  }

  /**
   * convert a sudden death victory conditions into a position medal
   */
  protected function extractSuddenDeathDatas($condition)
  {
    $hexes = [];
    foreach ($condition['group'] as $g) {
      $hexes[] = Utils::revertCoords($g);
    }

    return [
      'location' => 'board',
      'x' => $hexes[0]['x'],
      'y' => $hexes[0]['y'],
      'sprite' => $condition['side'] == ALLIES ? 'medal1' : 'medal2',
      'team' => $condition['side'],
      'type' => \TOKEN_MEDAL,
      'datas' => json_encode([
        'permanent' => true,
        'counts_for' => \INFINITY,
        'nbr_hex' => $condition['number'] ?? 1,
        'group' => $hexes,
      ]),
    ];
  }

  public function getBoardMedals()
  {
    return self::getSelectQuery()
      ->where('type', \TOKEN_MEDAL)
      ->get();
  }

  public function removeTargets($coords)
  {
    $tokens = self::getSelectQuery()
      ->where('x', $coords['x'])
      ->where('y', $coords['y'])
      ->where('type', \TOKEN_TARGET)
      ->get();

    foreach ($tokens as $t) {
      self::DB()->delete($t['id']);
      Notifications::removeToken($t);
    }
  }

  public function removeCamouflage($coords)
  {
    $tokens = self::getSelectQuery()
      ->where('x', $coords['x'])
      ->where('y', $coords['y'])
      ->where('type', \TOKEN_CAMOUFLAGE)
      ->get();

    foreach ($tokens as $t) {
      self::DB()->delete($t['id']);
      Notifications::removeToken($t);
    }
  }
}
