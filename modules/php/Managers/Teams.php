<?php
namespace M44\Managers;
use M44\Core\Globals;
use M44\Core\Stats;
use M44\Models\Team;
use M44\Core\Notifications;
use M44\Core\Game;
use M44\Helpers\Utils;

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
      $teamId = $rematch ? 2 - $i : $i - 1;
      $team = $info['side_player' . $i];

      self::DB()->insert([
        'team' => $team,
        'position' => $i,
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
          $players[$pId]->setTeam($team);
        }
      }
    }
  }

  public function checkVictory()
  {
    foreach (self::getAll() as $team) {
      if ($team->getNVictory() <= $team->getMedals()->count()) {
        foreach ($team->getMembers() as $member) {
          // log victory
          $method = 'setStatusRound' . Globals::getRound();
          Stats::$method($member->getId(), 1);
          $method = 'setTeamRound' . Globals::getRound();
          Stats::$method($member->getId(), $team->getId() == ALLIES ? 0 : 1);
        }

        foreach ($team->getOpponent()->getMembers() as $member) {
          // log defeat
          $method = 'setStatusRound' . Globals::getRound();
          Stats::$method($member->getId(), 0);
          $method = 'setTeamRound' . Globals::getRound();
          Stats::$method($member->getId(), $team->getId() == ALLIES ? 1 : 0);
        }

        Notifications::winRound($team, Globals::getRound());
        Game::get()->gamestate->jumpToState(ST_NEW_ROUND);
        return true;
      }
    }

    // sudden death
    $suddenDeath = Globals::getSuddenDeath();
    if ($suddenDeath != null) {
      $team = Teams::get($suddenDeath['side']);
      $n = 0;
      foreach ($team->getUnits() as $unit) {
        if (in_array(Utils::computeCoords($unit->getPos()), $suddenDeath['group'])) {
          $n++;
        }
      }
      if ($n >= $suddenDeath['number']) {
        $team->addSuddenDeathMedals();
        return self::checkVictory();
      }
    }
    return false;
  }
}
