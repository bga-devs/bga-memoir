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
use M44\Models\Terrain;

/*
 * Medals manager
 */

class Medals extends \M44\Helpers\DB_Manager
{
  protected static $table = 'medals';
  protected static $primary = 'id';
  protected static function cast($row)
  {
    if (($row['type'] == \MEDAL_ELIMINATION && isset($row['foreign_id']))
      || ($row['type'] == \MEDAL_EXIT && isset($row['foreign_id']))
    ) {
      $unit = Units::get($row['foreign_id']);
      $row['unit_type'] = $unit->getType();
      $row['unit_nation'] = $unit->getNation();
      $row['unit_badge'] = $unit->getBadge();
    }

    if (isset($row['group'])) {
      $row['group'] = \json_decode($row['group'], true);
    }

    return $row;
  }

  public static function getOfTeam($team)
  {
    return self::DB()
      ->where('team', $team)
      ->get();
  }

  public static function addEliminationMedals($team, $nMedals, $unit)
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

  public static function addExitMedals($team, $nMedals, $unit)
  {
    $ids = [];
    for ($i = 0; $i < $nMedals; $i++) {
      $ids[] = self::DB()->insert([
        'team' => $team,
        'type' => MEDAL_EXIT,
        'foreign_id' => $unit->getId(),
        'sprite' => $team == ALLIES ? 'medal8' : 'medal9',
      ]);
    }

    return self::DB()
      ->whereIn('id', $ids)
      ->get();
  }

  public static function addDestroyedTerrainMedals($team, $nMedals)
  {
    $ids = [];
    for ($i = 0; $i < $nMedals; $i++) {
      $ids[] = self::DB()->insert([
        'team' => $team,
        'type' => MEDAL_ELIMINATION,
        'foreign_id' => NULL,
        'sprite' => $team == ALLIES ? 'medal1' : 'medal2',
      ]);
    }

    return self::DB()
      ->whereIn('id', $ids)
      ->get();
  }

  public static function addDecoyMedals($team, $terrain)
  {
    $ids = [];

    $ids[] = self::DB()->insert([
      'team' => $team,
      'type' => \MEDAL_DECOY,
      'foreign_id' => $terrain->getId(),
      'sprite' => $team == ALLIES ? 'medal8' : 'medal9',
    ]);
    $team = Teams::get($team);

    // Increase stats
    $statName = 'incMedalRound' . Globals::getRound();
    foreach ($team->getMembers() as $player) {
      Stats::$statName($player, 1);
    }

    return self::DB()
      ->whereIn('id', $ids)
      ->get();
  }

  public static function addSuddenDeathMedals($team, $nMedals)
  {
    $ids = [];
    for ($i = 0; $i < $nMedals; $i++) {
      $ids[] = self::DB()->insert([
        'team' => $team,
        'type' => \MEDAL_SUDDEN_DEATH,
        'foreign_id' => 0,
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
  public static function loadScenario($scenario, $rematch)
  {
    self::DB()
      ->delete()
      ->run();
  }

  /******************************
   ******** Board Medals *********
   ******************************/
  public static function getBoardMedalHolder($mId)
  {
    $medal = self::DB()
      ->where('type', MEDAL_POSITION)
      ->where('foreign_id', $mId)
      ->getSingle();

    return is_null($medal) ? null : $medal['team'];
  }

  public static function checkBoardMedals($startOfTurn = false)
  {
    foreach (Tokens::getBoardMedals() as $medal) {
      $datas = $medal['datas'];
      $currentHolder = self::getBoardMedalHolder($medal['id']);
      if ($currentHolder != null && $datas['permanent']) {
        continue; // No need to check gained permanent medals
      }
      // Manage turn start for Panzer versus Grants next start turn medal rule (for AXIS team in this specific scenario)
      if ((($datas['turn_start'] ?? false) && !$startOfTurn)
        || (($datas['turn_start'] ?? false) && $startOfTurn && (Globals::getTeamTurn() != $medal['team']))
      ) {
        continue; // No need to check startOfTurn medals if not at start of turn
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

      // Is this a majority medal ?
      if ($newHolder != null && $datas['majority']) {
        if ($nHexes[ALLIES] > $nHexes[AXIS]) {
          if(isset($datas['side'])){
            if ($datas['side'] == ALLIES) {
              $newHolder = ALLIES;
            } else {
              $newHolder = null;
            }
          } else {
            $newHolder = ALLIES;
          }          
        } elseif ($nHexes[AXIS] > $nHexes[ALLIES]) {
          if(isset($datas['side'])){
            if ($datas['side'] == AXIS) {
              $newHolder = AXIS;
            } else {
              $newHolder = null;
            }
          } else {
            $newHolder = AXIS;
          }          
        } else {
          $newHolder = null;
        }
      }

      // Is this a sole control medal ?
      if ($newHolder != null && $datas['sole_control']) {
        if (min($nHexes) > 0) {
          $newHolder = null;
        }
      }

      // Has the owner changed ?
      if ($currentHolder != $newHolder) {
        // Remove the medal of old owner
        if ($currentHolder !== null) {
          // Remove only if new owner or if it's not a 'lastToOccupy' medal
          if ($newHolder != null || !$datas['last_to_occupy']) {
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

          // For Panzer versus Grants next start turn also remove Terrain (HQ)
          if ($datas['turn_start'] ?? false) {
            foreach (Board::getTerrainsInCell($medal['x'], $medal['y']) as $t) {
              if ($t instanceof \M44\Terrains\HQ) {
                $t->removeFromBoard();
              }
            }
          }
        }
      }
    }

    self::checkEmptySectionMedal();
  }

  public static function checkEmptySectionMedal()
  {
    $data = Globals::getEmptySectionMedals();
    if (is_null($data) || empty($data)) {
      return;
    }
    $team = Teams::get($data['side']);

    // Check if a section is cleared
    $opponent = Teams::get($data['side'])
      ->getOpponent()
      ->getId();
    $cleared = false;
    foreach ($data['sections'] as $section) {
      if (Units::getInSection($opponent, $section)->count() == 0) {
        $cleared = true;
      }
    }

    // Fetch the winned emptySectionMedals
    $medalIds = self::DB()
      ->where('type', MEDAL_EMPTY_SECTION)
      ->where('team', $data['side'])
      ->get()
      ->getIds();
    $hasMedal = !empty($medalIds);

    // If no medal yet and a section is cleared
    if ($cleared && !$hasMedal) {
      $nMedals = $team->getMedals()->count();
      $medalsObtained = $data['count_for'];
      if ($nMedals + $medalsObtained > $team->getNVictory()) {
        // Can't get more medals that winning condition
        $medalsObtained = $team->getNVictory() - $nMedals;
      }

      $ids = [];
      for ($i = 0; $i < $medalsObtained; $i++) {
        $ids[] = self::DB()->insert([
          'team' => $data['side'],
          'type' => \MEDAL_EMPTY_SECTION,
          'foreign_id' => null,
          'sprite' => $data['side'] == ALLIES ? 'medal1' : 'medal2',
        ]);
      }
      // Increase stats
      $statName = 'incMedalRound' . Globals::getRound();
      foreach ($team->getMembers() as $player) {
        Stats::$statName($player, $medalsObtained);
      }

      Notifications::scoreSectionMedals(
        $team->getId(),
        self::DB()
          ->whereIn('id', $ids)
          ->get()
      );
    }
    // If a medals was won but no section are cleared now => lose the medals
    elseif (!$cleared && $hasMedal) {
      // remove medals - prettier-ignore
      self::DB()->delete()->whereIn('id', $medalIds)->run();
      Notifications::removeSectionMedals($team->getId(), $medalIds);

      // Decrease stats
      $statName = 'incMedalRound' . Globals::getRound();
      foreach ($team->getMembers() as $player) {
        Stats::$statName($player, -count($medalIds));
      }
    }
  }

  public static function addPositionMedals($team, $nMedals, $boardMedal)
  {
    $ids = [];
    $sprite = $boardMedal['sprite'];
    if ($sprite == 'medal0') {
      // Handle the case of 'both team' medal
      $sprite = $team == ALLIES ? 'medal1' : 'medal2';
    }

    for ($i = 0; $i < $nMedals; $i++) {
      $ids[] = self::DB()->insert([
        'team' => $team,
        'type' => \MEDAL_POSITION,
        'foreign_id' => $boardMedal['id'],
        'sprite' => $sprite,
      ]);
    }

    return self::DB()
      ->whereIn('id', $ids)
      ->get();
  }

  public static function removePositionMedals($boardMedal)
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
