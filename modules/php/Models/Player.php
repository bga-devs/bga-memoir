<?php
namespace M44\Models;
use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Core\Preferences;
use M44\Helpers\Utils;
use M44\Managers\Cards;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Units;

/*
 * Player: all utility functions concerning a player
 */

class Player extends \M44\Helpers\DB_Model
{
  protected $table = 'player';
  protected $primary = 'player_id';
  protected $attributes = [
    'id' => 'player_id',
    'no' => 'player_no',
    'name' => 'player_name',
    'color' => 'player_color',
    'eliminated' => 'player_eliminated',
    'score' => 'player_score',
    'zombie' => 'player_zombie',
    'team' => 'team_side',
  ];

  /*
   * Getters
   */
  public function getPref($prefId)
  {
    return Preferences::get($this->id, $prefId);
  }

  public function jsonSerialize($currentPlayerId = null)
  {
    $data = parent::jsonSerialize();
    $current = $this->id == $currentPlayerId;
    $data = array_merge($data, [
      'cards' => $current ? $this->getCards()->toArray() : [],
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

  public function getCardInPlay()
  {
    return Cards::getInPlayOfPlayer($this->id);
  }

  public function getTeam()
  {
    return Teams::getSide($this->team);
  }

  public function getUnitsInSection($section)
  {
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
