<?php
namespace M44\Models;
use M44\Core\Globals;
use M44\Core\Stats;
use M44\Core\Notifications;
use M44\Helpers\Utils;
use M44\Managers\Cards;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Units;
use M44\Managers\Tokens;
use M44\Managers\Medals;
use M44\Scenario;

/*
 * Team: all utility functions concerning a team
 */

class Team extends \M44\Helpers\DB_Model
{
  protected $table = 'teams';
  protected $primary = 'team';
  protected $attributes = [
    'id' => 'team',
    'position' => ['position', 'int'],
    'country' => 'country',
    'nCards' => ['cards', 'int'],
    'nVictory' => ['victory', 'int'],
    'nReserveTokens' => ['reserve_tokens', 'int'],
    'leftPId' => ['left_pId', 'int'],
    'centralPId' => ['central_pId', 'int'],
    'rightPId' => ['right_pId', 'int'],
    'commanderPId' => 'commander_pId',
  ];

  public function jsonSerialize($currentPlayerId = null)
  {
    return [
      'team' => $this->id,
      'position' => $this->position,
      'medals' => $this->getMedals(),
      'victory' => $this->nVictory,
      'reserve_tokens' => $this->nReserveTokens,
      'units_on_reserve' => Units::getOfTeamOnReserve($this->id),
      'air_power_tokens' => $this->getAirPowerTokens(),
    ];
  }

  public function getMedals()
  {
    return Medals::getOfTeam($this->id);
  }

  public function getReserveTokens()
  {
    return Globals::isCampaign() ? $this->nReserveTokens : 0;
  }

  public function getAirPowerTokens()
  {
    return Globals::isCampaign() && in_array($this->id, Globals::getAirPowerTokens()) ? 1 : 0;
  }

  public function hasAirPowerTokens()
  {
    return $this->getAirPowerTokens() > 0;
  }

  public function getCountry()
  {
    return $this->country;
  }

  public function getMembers()
  {
    return Players::getOfTeam($this->id);
  }

  public function getOpponent()
  {
    $otherTeam = $this->id == ALLIES ? AXIS : ALLIES;
    return Teams::get($otherTeam);
  }

  public function getUnits()
  {
    return Units::getOfTeam($this->id);
  }

  public function getPlayerInCharge($section)
  {
    $names = [
      0 => 'leftPId',
      1 => 'centralPId',
      2 => 'rightPId',
    ];
    $name = $names[$section];
    return Players::get($this->$name);
  }

  public function getCommander()
  {
    return Players::get($this->commanderPId);
  }

  public function addEliminationMedals($unit)
  {
    $nMedals = $this->getMedals()->count();
    $medalsObtained = $unit->getMedalsWorth();
    if ($nMedals + $medalsObtained > $this->nVictory) {
      // Can't get more medals that winning condition
      $medalsObtained = $this->nVictory - $nMedals;
    }

    if ($medalsObtained == 0) {
      // No medal to add, abort
      return;
    }

    // Increase stats
    $statName = 'incMedalRound' . Globals::getRound();
    foreach ($this->getMembers() as $player) {
      Stats::$statName($player, $medalsObtained);
    }

    $medals = Medals::addEliminationMedals($this->id, $medalsObtained, $unit);
    Notifications::scoreMedals($this->id, $medals, $unit->getPos());
  }

  public function addSuddenDeathMedals()
  {
    $nMedals = $this->getMedals()->count();
    $medalsObtained = $this->getNVictory() - $nMedals;

    // Increase stats
    $statName = 'incMedalRound' . Globals::getRound();
    foreach ($this->getMembers() as $player) {
      Stats::$statName($player, $medalsObtained);
    }

    $medals = Medals::addSuddenDeathMedals($this->id, $medalsObtained);
    Notifications::scoreMedals($this->id, $medals, ['x' => 1, 'y' => 1]);
  }

  public function addExitMedals($unit)
  {
    $nMedals = $this->getMedals()->count();
    $marker = Tokens::getOnCoords('board', $unit->getPos(), \TOKEN_EXIT_MARKER)->first();

    if ($unit->getType() == \INFANTRY) {
      $medalsObtained = $marker['datas']['medals'];
    } else {
      $medalsObtained = 1;
    }

    $must_have_exit = Globals::getMustHaveExitUnit();
    $medal_bonus = !is_null($must_have_exit) && $must_have_exit['side'] == $unit->getTeam()->getId() ?
      $must_have_exit['min_nbr_units'] : 0;
    if ($nMedals + $medalsObtained > $this->nVictory) {
      // Can't get more medals that winning condition (except if must have exit option)
      $medalsObtained = $this->nVictory + $medal_bonus - $nMedals;
    }

    if ($medalsObtained == 0) {
      // No medal to add, abort
      return;
    }

    if ($nMedals + $medalsObtained <= $this->nVictory) {
      // Increase stats
      $statName = 'incMedalRound' . Globals::getRound();
      foreach ($this->getMembers() as $player) {
        Stats::$statName($player, $medalsObtained);
      }
    }

    $medals = Medals::addExitMedals($this->id, $medalsObtained, $unit);
    Notifications::scoreMedals($this->id, $medals, $unit->getPos());
  }
}
