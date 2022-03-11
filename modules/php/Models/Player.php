<?php
namespace M44\Models;
use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Core\Preferences;
use M44\Core\Stats;
use M44\Helpers\Utils;
use M44\Managers\Cards;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Units;
use M44\Scenario;

/*
 * Player: all utility functions concerning a player
 */

class Player extends \M44\Helpers\DB_Model
{
  protected $table = 'player';
  protected $primary = 'player_id';
  protected $attributes = [
    'id' => ['player_id', 'int'],
    'no' => ['player_no', 'int'],
    'name' => 'player_name',
    'color' => 'player_color',
    'eliminated' => 'player_eliminated',
    'score' => ['player_score', 'int'],
    'scoreAux' => ['player_score_aux', 'int'],
    'zombie' => 'player_zombie',
    'team' => 'player_team',
  ];

  /*
   * Getters
   */
  public function getPref($prefId)
  {
    return Preferences::get($this->id, $prefId);
  }

  public function getStat($name)
  {
    $name = 'get' . \ucfirst($name);
    return Stats::$name($this->id);
  }

  public function jsonSerialize($currentPlayerId = null)
  {
    $data = parent::jsonSerialize();
    $current = $this->id == $currentPlayerId;
    $data = array_merge($data, [
      'cards' => $current ? $this->getCards()->toArray() : [],
      'cardsCount' => $this->getCards()->count() + $this->getCardsChoice()->count(),
      'inplay' => $this->getCardInPlay(),
    ]);

    return $data;
  }

  public function getId()
  {
    return (int) parent::getId();
  }

  public function getCards()
  {
    return Cards::getOfPlayer($this->id);
  }

  public function getCardsChoice()
  {
    return Cards::getInLocation(['choice', $this->id]);
  }

  public function getCardInPlay()
  {
    return Cards::getInPlayOfPlayer($this->id);
  }

  public function getTeam()
  {
    return Teams::get($this->team);
  }

  public function getUnits()
  {
    return Units::getOfTeam($this->team);
  }

  public function getUnitsInSection($section)
  {
    if (Scenario::getTopTeam() == $this->team) {
      $section = 2 - $section;
    }
    return Units::getInSection($this->team, $section);
  }

  public function getUnitsBySections()
  {
    $units = [];
    for ($i = 0; $i < 3; $i++) {
      $units[$i] = $this->getUnitsInSection($i);
    }

    return $units;
  }
}
