<?php
namespace M44\Managers;
use M44\Core\Globals;
use M44\Models\Team;

/**
 * Teams
 */
class Teams extends \M44\Helpers\DB_Manager
{
  protected static $table = 'teams';
  protected static $primary = 'team';
  protected static function cast($row)
  {
    return new Team($row);
  }

  public function getAll()
  {
    return self::DB()->get();
  }

  public function get($team)
  {
    return self::DB()
      ->where('team', $team)
      ->getSingle();
  }

  public function getTeamTurn()
  {
    return self::get(Globals::getTeamTurn());
  }

  public function changeTeamTurn()
  {
    $currentTeam = Globals::getTeamTurn();
    $newTeam = $currentTeam == ALLIES ? AXIS : ALLIES;
    Globals::setTeamTurn($newTeam);
  }

  /**
   * Load a scenario
   */
  public function loadScenario($scenario, $rematch)
  {
    self::DB()
      ->delete()
      ->run();

    // Get team composition
    $composition = Players::getTeamsComposition();
    $players = Players::getAll();

    // Create teams
    $info = $scenario['game_info'];
    for ($i = 1; $i <= 2; $i++) {
      $teamId = $rematch ? (2 - $i) : ($i - 1);

      self::DB()->insert([
        'team' => $info['side_player' . $i],
        'country' => $info['country_player' . $i] ?? '',
        'cards' => $info['cards_player' . $i],
        'victory' => $info['victory_player' . $i],
        'left_pId' => $composition[$teamId][0],
        'central_pId' => $composition[$teamId][1],
        'right_pId' => $composition[$teamId][2],
        'commander_pId' => $composition[$teamId][3],
      ]);

      foreach ($composition[$teamId] as $pId) {
        if ($pId !== null) {
          $players[$pId]->setTeam($info['side_player' . $i]);
        }
      }
    }
  }
}
