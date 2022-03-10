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
    'country' => 'country',
    'nCards' => ['cards', 'int'],
    'nVictory' => ['victory', 'int'],
    'leftPId' => ['left_pId', 'int'],
    'centralPId' => ['central_pId', 'int'],
    'rightPId' => ['right_pId', 'int'],
    'commanderPId' => 'commander_pId',
  ];

  public function jsonSerialize($currentPlayerId = null)
  {
    return [
      'team' => $this->id,
      'medals' => $this->getMedals(),
      'victory' => $this->nVictory,
    ];
  }

  public function getMedals()
  {
    return Medals::getOfTeam($this->id);
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
      Stats::$statName($player, 1);
    }

    $medals = Medals::addEliminationMedals($this->id, $medalsObtained, $unit);
    Notifications::scoreMedals($this->id, $medals, $unit->getPos());
  }
}
