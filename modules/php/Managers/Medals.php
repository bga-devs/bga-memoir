<?php
namespace M44\Managers;
use M44\Core\Game;
use M44\Core\Globals;
use M44\Core\Preferences;
use M44\Helpers\Collection;

/*
 * Medals manager
 */
class Medals extends \M44\Helpers\DB_Manager
{
  protected static $table = 'medals';
  protected static $primary = 'id';
  protected static function cast($row)
  {
    $row['extra_datas'] = json_decode($row['extra_datas'], true);
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

    // TODO : use global to store info about medals linked to position
  }

  public function addEliminationMedals($team, $nMedals, $unit)
  {
    $ids = [];
    for ($i = 0; $i < $nMedals; $i++) {
      $ids[] = self::DB()->insert([
        'team' => $team,
        'type' => MEDAL_ELIMINATION,
        'sprite' => $team == ALLIES ? 'medal8' : 'medal9',
        'extra_datas' => \json_encode([
          'type' => $unit->getType(),
          'nation' => $unit->getNation(),
        ]),
      ]);
    }

    return self::DB()
      ->whereIn('id', $ids)
      ->get();
  }
}
