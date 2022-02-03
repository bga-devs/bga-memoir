<?php
namespace M44\Managers;
use M44\Core\Globals;

/**
 * Teams
 */
class Teams extends \M44\Helpers\DB_Manager
{
  protected static $table = 'teams';
  protected static $primary = 'side';
  protected static function cast($row)
  {
    return $row;
  }

  public function getAll()
  {
    return self::DB()->get();
  }

  public function getSide($side)
  {
    return self::DB()
      ->where('side', $side)
      ->getSingle();
  }

  public function getSideTurn()
  {
    return self::getSide(Globals::getSideTurn());
  }

  /**
   * Load a scenario
   */
  public function loadScenario($scenario, $rematch)
  {
    self::DB()
      ->delete()
      ->run();

    // Create teams
    $info = $scenario['game_info'];
    self::DB()->insert([
      'side' => $info['side_player1'],
      'country' => $info['country_player1'] ?? '',
      'cards' => $info['cards_player1'],
      'medals' => 0,
      'victory' => $info['victory_player1'],
    ]);
    self::DB()->insert([
      'side' => $info['side_player2'],
      'country' => $info['country_player2'] ?? '',
      'cards' => $info['cards_player2'],
      'medals' => 0,
      'victory' => $info['victory_player2'],
    ]);

    // Assign teams to players
    $players = Players::getAll()->toArray();
    $players[0]->setTeam($rematch ? $info['side_player2'] : $info['side_player1']);
    $players[1]->setTeam($rematch ? $info['side_player1'] : $info['side_player2']);
  }
}
