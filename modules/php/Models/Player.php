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
use M44\Board;

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
      'isCommissar' => $this->isCommissar(),
      'commissarCard' => $current ? $this->getCommissarCard() : $this->getCommissarCard() != null,
      'nreservetoken' => $this->getReserveTokens(),
      'isCampaign' => Globals::isCampaign(),
      'campaignNation' => Globals::isCampaign() ? Globals::getCampaign()['scenarios'][$this->getTeam()->getId()]['country'] : NULL,
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

  public function countAllCards()
  {
    $inPlay = $this->getCardInPlay() ? 1 : 0;
    $commissar = $this->getCommissarCard() ? 1 : 0;
    return $this->getCards()->count() + $inPlay + $commissar;
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

  public function canHill317()
  {
    if (((Globals::isBlitz() && $this->team == \AXIS) 
      || (!is_null(Globals::getAirStrikes()) && $this->team == Globals::getAirStrikes()))
      && Globals::getNightVisibility() >= 6) {
      return true;
    }

    if ($this->team == \AXIS) {
      return false;
    }

    foreach (self::getUnits() as $unit) {
      if (Board::cellHasProperty($unit->getPos(), 'hill317', $unit)) {
        return true;
      }
    }
    return false;
  }

  public function canBlowBridge() {
    $blowbridge = Globals::getBlowBridgeOpt2();
    return !is_null($blowbridge) && $blowbridge['side'] == Globals::getTeamTurn();
  }

  public function canArmorBreakthrough() {
    $armorBreakthrough = Globals::getArmorBreakthrough();
    $team = $this->getTeam()->getId();
    $armorBreakthroughDone = isset(Globals::getArmorBreakthroughDone()[$team]) ? 
      Globals::getArmorBreakthroughDone()[$team] : true ;
    
    // TODO , si ce joueur est eligible dans le scenario
    // et si ce n'est pas deja fait pour ce joueur (Global Variable)
    
    return !is_null($armorBreakthrough) && isset($armorBreakthrough[$team]) && !$armorBreakthroughDone;
  }

  /**************
   * Commissar
   *************/

  public function isCommissar()
  {
    $team = Globals::getCommissar();
    return $team != '' && $team == $this->team && $this->getTeam()->getCommanderPId() == $this->id;
  }

  public function getCommissarCard()
  {
    return Cards::getInLocation(['commissar', $this->id])->first();
  }

  public function isMarineCommand()
  {
    if ($this->getTeam()->getCountry() == 'US' && Globals::isMarineCommand()) {
      return true;
    }
    return false;
  }

  /*****************
  * Reserve Tokens
  *****************/
  public function getReserveTokens()
  {
    return Globals::isCampaign() ? $this->getTeam()->getReserveTokens() : 0;
  }

}
