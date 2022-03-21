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
  // protected static $primary = 'id';
  protected static $prefix = 'token_';
  protected static $customFields = [
    'team',
    'x',
    'y',
    'sprite',
    'type',
    'tag',
    'permanent',
    'counts_for',
    'nbr_hex',
    'group',
  ];
  protected static $autoreshuffle = false;
  protected static function cast($row)
  {
    if (isset($row['group'])) {
      $row['group'] = \json_decode($row['group'], true);
    }

    return $row;
  }

  public function getOfTeam($team)
  {
    return self::DB()
      ->where('team', $team)
      ->get();
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
    $boardMedals = [];
    foreach ($board['hexagons'] as $hex) {
      $tags = $hex['tags'] ?? [];
      foreach ($tags as $tag) {
        if (strpos($tag['name'], 'medal') === 0) {
          $team = in_array($tag['name'], ['medal1', 'medal4', 'medal6']) ? ALLIES : AXIS;
          $permanent = $tag['medal']['permanent'] ?? false;
          $hexes = [['x' => $hex['col'], 'y' => $hex['row']]];
          if (isset($tag['group']) && !empty($tag['group'])) {
            foreach ($tag['group'] as $g) {
              $hexes[] = Utils::revertCoords($g);
            }
          }

          $boardMedals[] = [
            'x' => $hex['col'],
            'y' => $hex['row'],
            'location' => 'board_medal',
            'team' => $team,
            'sprite' => $tag['name'],
            'type' => 0,
            'permanent' => $permanent ? 1 : 0,
            'counts_for' => $tag['medal']['counts_for'] ?? 1,
            'nbr_hex' => $tag['medal']['nbr_hex'] ?? 1,
            'group' => \json_encode($hexes),
          ];
        }
      }
    }

    self::create($boardMedals);
  }

  public static function addTagClause(&$q, $tag)
  {
    $q = $q->where('tag', $tag);
  }

  /******************************
   ******** Board Medals *********
   ******************************/
  public function getOnBoardMedals()
  {
    return self::getInLocation('board_medal');
    return $query->get();
  }
}
