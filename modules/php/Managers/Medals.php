<?php
namespace M44\Managers;
use M44\Core\Game;
use M44\Core\Stats;
use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Core\Preferences;
use M44\Helpers\Collection;
use M44\Managers\Units;
use M44\Board;
use M44\Helpers\Utils;
use M44\Managers\Tokens;

/*
 * Medals manager
 */
class Medals extends \M44\Helpers\DB_Manager
{
  protected static $table = 'medals';
  protected static $primary = 'id';
  protected static function cast($row)
  {
    if ($row['type'] == \MEDAL_ELIMINATION && isset($row['foreign_id'])) {
      $unit = Units::get($row['foreign_id']);
      $row['unit_type'] = $unit->getType();
      $row['unit_nation'] = $unit->getNation();
    }

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

  public function addEliminationMedals($team, $nMedals, $unit)
  {
    $ids = [];
    for ($i = 0; $i < $nMedals; $i++) {
      $ids[] = self::DB()->insert([
        'team' => $team,
        'type' => MEDAL_ELIMINATION,
        'foreign_id' => $unit->getId(),
        'sprite' => $team == ALLIES ? 'medal8' : 'medal9',
      ]);
    }

    return self::DB()
      ->whereIn('id', $ids)
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
  }

  /******************************
   ******** Board Medals *********
   ******************************/
  public function getBoardMedalHolder($mId)
  {
    $medal = self::DB()
      ->where('type', MEDAL_POSITION)
      ->where('foreign_id', $mId)
      ->getSingle();

    return is_null($medal) ? null : $medal['team'];
  }

  public function checkBoardMedals()
  {
    foreach (Tokens::getBoardMedals() as $medal) {
      $datas = $medal['datas'];
      $currentHolder = self::getBoardMedalHolder($medal['id']);
      if ($currentHolder != null && $datas['permanent']) {
        continue; // No need to check gained permanent medals
      }

      // Compute the nbr of hexes owned by each nation
      $nHexes = [ALLIES => 0, AXIS => 0];
      foreach ($datas['group'] as $hex) {
        $unit = Board::getUnitInCell($hex);
        if ($unit !== null) {
          $nHexes[$unit->getTeam()->getId()]++;
        }
      }

      // Is there a new medal owner ?
      $newHolder = null;
      if ($nHexes[ALLIES] >= $datas['nbr_hex'] && $medal['team'] != AXIS) {
        $newHolder = ALLIES;
      } elseif ($nHexes[AXIS] >= $datas['nbr_hex'] && $medal['team'] != ALLIES) {
        $newHolder = AXIS;
      }

      // Has the owner changed ?
      if ($currentHolder != $newHolder) {
        // Remove the medal of old owner, if any
        if ($currentHolder !== null) {
          // Remove the medals
          $medalIds = self::removePositionMedals($medal);
          Notifications::removeMedals($currentHolder, $medalIds, $medal);

          // Decrease stats
          $team = Teams::get($currentHolder);
          $statName = 'incMedalRound' . Globals::getRound();
          foreach ($team->getMembers() as $player) {
            Stats::$statName($player, -count($medalIds));
          }
        }

        // Add the medal to new owner, if any
        if ($newHolder !== null) {
          $nMedals = $datas['counts_for'];
          $team = Teams::get($newHolder);
          $nMedals = min($nMedals, $team->getNVictory() - $team->getMedals()->count());
          if ($nMedals == 0) {
            continue;
          }

          // Increase stats
          $statName = 'incMedalRound' . Globals::getRound();
          foreach ($team->getMembers() as $player) {
            Stats::$statName($player, $nMedals);
          }

          // Create medals and notify them
          $medals = self::addPositionMedals($newHolder, $nMedals, $medal);
          Notifications::scoreMedals($newHolder, $medals);
        }
      }
    }
  }

  public function addPositionMedals($team, $nMedals, $boardMedal)
  {
    $ids = [];
    for ($i = 0; $i < $nMedals; $i++) {
      $ids[] = self::DB()->insert([
        'team' => $team,
        'type' => \MEDAL_POSITION,
        'foreign_id' => $boardMedal['id'],
        'sprite' => $boardMedal['sprite'],
      ]);
    }

    return self::DB()
      ->whereIn('id', $ids)
      ->get();
  }

  public function removePositionMedals($boardMedal)
  {
    $ids = self::DB()
      ->where('type', MEDAL_POSITION)
      ->where('foreign_id', $boardMedal['id'])
      ->get()
      ->getIds();

    self::DB()
      ->delete()
      ->where('type', MEDAL_POSITION)
      ->where('foreign_id', $boardMedal['id'])
      ->run();
    return is_array($ids) ? $ids : [$ids];
  }
}
