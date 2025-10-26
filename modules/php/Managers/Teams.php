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

  public static function getAll()
  {
    return self::DB()->get();
  }

  public static function get($team)
  {
    return self::DB()
      ->where('team', $team)
      ->getSingle();
  }

  public static function getTeamTurn()
  {
    return self::get(Globals::getTeamTurn());
  }

  public static function changeTeamTurn()
  {
    $currentTeam = mb_strtoupper(Globals::getTeamTurn());
    $newTeam = $currentTeam == ALLIES ? AXIS : ALLIES;
    Globals::setTeamTurn($newTeam);
  }

  /**
   * Load a scenario
   */
  public static function loadScenario($scenario, $rematch)
  {
    self::DB()
      ->delete()
      ->run();

    // Get team composition
    $composition = Players::getTeamsComposition();
    $players = Players::getAll();
    $info = $scenario['game_info'];

    // Flip teams if forced team for one way game
    $forced = Globals::getForcedTeam();
    if (!$rematch && isset($forced['pId'])) {
      $j = in_array($forced['pId'], $composition[0]) ? 1 : 2;
      if ($info['side_player' . $j] != $forced['team']) {
        $rematch = true;
      }
    }

    // Create teams
    for ($i = 1; $i <= 2; $i++) {
      $teamId = $rematch ? 2 - $i : $i - 1;
      $team = mb_strtoupper($info['side_player' . $i]);
      $info['country_player' . $i] = $info['country_player' . $i] ?? '';
      if (Globals::isCampaign()) {
        $step = Globals::getCampaignStep();
        $currentReserveTokens = isset(Globals::getCampaign()['scenarios'][$team]['reserve_tokens']['current']) ? 
          Globals::getCampaign()['scenarios'][$team]['reserve_tokens']['current'] : 0;
        self::DB()->insert([
          'team' => $team,
          'position' => $i,
          'country' => mb_strtoupper($info['country_player' . $i]) ?? '',
          'cards' => Globals::isItalyHighCommand() && $team == AXIS ? 6 : $info['cards_player' . $i],
          'victory' => $info['victory_player' . $i],
          'reserve_tokens' => $currentReserveTokens + 
            Globals::getCampaign()['scenarios'][$team]['reserve_tokens'][$step],
          'left_pId' => $composition[$teamId][0],
          'central_pId' => $composition[$teamId][1],
          'right_pId' => $composition[$teamId][2],
          'commander_pId' => $composition[$teamId][3],
        ]);
      } else {
      self::DB()->insert([
        'team' => $team,
        'position' => $i,
        'country' => mb_strtoupper($info['country_player' . $i]) ?? '',
        'cards' => Globals::isItalyHighCommand() && $team == AXIS ? 6 : $info['cards_player' . $i],
        'victory' => $info['victory_player' . $i],
        'reserve_tokens' => 0,
        'left_pId' => $composition[$teamId][0],
        'central_pId' => $composition[$teamId][1],
        'right_pId' => $composition[$teamId][2],
        'commander_pId' => $composition[$teamId][3],
      ]);
    }
    
      foreach ($composition[$teamId] as $pId) {
        if ($pId !== null) {
          $players[$pId]->setTeam($team);
        }
      }
    }

    // Update stats
    foreach (self::getAll() as $team) {
      foreach ($team->getMembers() as $member) {
        $method = 'setTeamRound' . Globals::getRound();
        Stats::$method($member->getId(), $team->getId() == ALLIES ? 0 : 1);
      }
    }
  }

  public static function checkVictory()
  {
    foreach (self::getAll() as $team) {
      $must_have_exit = Globals::getMustHaveExitUnit();
      $nb_medal_exit = $team->getMedals()->filter(function ($m) {
        return $m['type'] == MEDAL_EXIT;
      })->count();
      $noOpponentRemainingUnits = $team->noOpponentRemainingUnits();

      $victory_condition = !is_null($must_have_exit) && $must_have_exit['side'] == $team->getId() ?
        ($team->getNVictory() <= $team->getMedals()->count() && $must_have_exit['min_nbr_units'] <= $nb_medal_exit) || $noOpponentRemainingUnits // add && nb medals exit >= $must_have_exit['min_nbr_units']
        : ($team->getNVictory() <= $team->getMedals()->count()) || $noOpponentRemainingUnits;

      if ($victory_condition) {
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
        if(!$noOpponentRemainingUnits) {
          Game::get()->gamestate->jumpToState(ST_END_OF_ROUND);
        }
        
        return true;
      }
      // Condition if the nb of medals are obtained but no exit medal
      if (
        !is_null($must_have_exit)
        && $must_have_exit['side'] == $team->getId()
        && $team->getNVictory() <= $team->getMedals()->count()
        && $must_have_exit['min_nbr_units'] > $nb_medal_exit
      ) {
        $txt = $team->getId() . clienttranslate('  player must have at least ') . $must_have_exit['min_nbr_units'] . clienttranslate('unit exit to win');
        Notifications::message($txt);
      }
    }

    return false;
  }

  public static function getWinner()
  {
    foreach (self::getAll() as $team) {
      $noOpponentRemainingUnits = $team->noOpponentRemainingUnits();
      if ($team->getNVictory() <= $team->getMedals()->count() || $noOpponentRemainingUnits) {
        return $team;
      }
    }
    return null;
  }
}
