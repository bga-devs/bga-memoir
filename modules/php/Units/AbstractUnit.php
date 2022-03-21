<?php
namespace M44\Units;

use M44\Board;
use M44\Scenario;
use M44\Managers\Players;
use M44\Managers\Units;
use M44\Managers\Cards;
use M44\Managers\Teams;

class AbstractUnit extends \M44\Helpers\DB_Model implements \JsonSerializable
{
  protected $table = 'units';
  protected $primary = 'unit_id';
  protected $attributes = [
    'id' => ['unit_id', 'int'],
    'x' => ['x', 'int'],
    'y' => ['y', 'int'],
    'nation' => 'nation',
    'nUnits' => ['figures', 'int'],
    'badge' => ['badge', 'int'],
    'moves' => ['moves', 'int'],
    'fights' => ['fights', 'int'],
    'retreats' => ['retreats', 'int'],
    'grounds' => ['grounds', 'int'],
    'activationCard' => 'activation_card',
    'extraDatas' => ['extra_datas', 'obj'],
  ];
  protected $staticAttributes = [
    'type',
    'statName',
    'name',
    'maxUnits',
    'movementRadius',
    'movementAndAttackRadius',
    'attackPower',
    'mustSeeToAttack',
    'maxGrounds',
    'medalsWorth',
    'retreatHex',
    'ignoreCannotBattle',
  ];

  protected $id = null;
  protected $x = null;
  protected $y = null;
  protected $sections = null;
  protected $nation = null;
  protected $nUnits = null;
  protected $badge = null;
  protected $datas = null;
  protected $activationCard = null;
  protected $moves = 0;
  protected $fights = 0;
  protected $grounds = 0;

  protected $type = null;
  protected $statName = null;
  protected $name = null;
  protected $maxUnits = null;
  protected $movementRadius = null;
  protected $movementAndAttackRadius = null;
  protected $attackPower = [];
  protected $mustSeeToAttack = true;
  protected $maxGrounds = 0;
  protected $medalsWorth = 1;
  protected $retreatHex = 1;
  protected $ignoreCannotBattle = false;

  public function __construct($row)
  {
    if ($row != null) {
      parent::__construct($row);
      $this->sections = $row['sections'];
      $this->datas = \json_decode($row['extra_datas'], true);
    }
  }

  public function jsonSerialize()
  {
    return [
      'id' => $this->id,
      'x' => $this->x,
      'y' => $this->y,
      'sections' => $this->sections,

      'type' => $this->type,
      'nation' => $this->nation,
      'name' => $this->name,
      'figures' => $this->nUnits,
      'badge' => $this->badge,
      'activationCard' => $this->activationCard,
      'onTheMove' => $this->datas['onTheMove'] ?? false,
    ];
  }

  /////////////////////////////////////////
  //    ____      _   _
  //  / ___| ___| |_| |_ ___ _ __ ___
  // | |  _ / _ \ __| __/ _ \ '__/ __|
  // | |_| |  __/ |_| ||  __/ |  \__ \
  //  \____|\___|\__|\__\___|_|  |___/
  /////////////////////////////////////////

  public function getPos()
  {
    return [
      'x' => $this->x,
      'y' => $this->y,
    ];
  }

  public function getTeamId()
  {
    return in_array($this->nation, Units::$nations[AXIS]) ? AXIS : ALLIES;
  }
  public function getTeam()
  {
    return Teams::get($this->getTeamId());
  }
  public function getPlayer()
  {
    $section = $this->sections[0];
    return $this->getTeam()->getPlayerInCharge($section);
  }

  public function isOpponent($unit)
  {
    $allies = ['fr', 'gb', 'us', 'ru', 'ch'];
    $a = in_array($this->nation, $allies);
    $b = in_array($unit->getNation(), $allies);
    return ($a && !$b) || (!$a && $b);
  }

  public function getCampDirection()
  {
    // Useful for retreat
    return in_array($this->nation, Units::$nations[Scenario::getTopTeam()]) ? -1 : 1;
  }

  public function getActivationOCard()
  {
    return is_null($this->activationCard) ? null : Cards::get($this->activationCard);
  }

  public function getSection()
  {
    return $this->sections;
  }

  public function isWounded()
  {
    return $this->maxUnits != $this->nUnits;
  }

  public function getAttackModifier($cell)
  {
    return 0;
  }

  //////////////////////////////////////
  //    ___  ____  ____  _____ ____
  //   / _ \|  _ \|  _ \| ____|  _ \
  //  | | | | |_) | | | |  _| | |_) |
  //  | |_| |  _ <| |_| | |___|  _ <
  //   \___/|_| \_\____/|_____|_| \_\
  //////////////////////////////////////

  public function activate($card, $onTheMove = false)
  {
    $cardId = is_int($card) ? $card : $card->getId();
    $this->setActivationCard($cardId);
    $this->setExtraDatas('onTheMove', $onTheMove);
  }

  public function getActivationPlayer()
  {
    if ($this->getActivationCard() != null) {
      return $this->getActivationOCard()->getPlayer();
    } else {
      return null;
    }
  }

  /////////////////////////////////
  //  __  __  _____     _______
  // |  \/  |/ _ \ \   / / ____|
  // | |\/| | | | \ \ / /|  _|
  // | |  | | |_| |\ V / | |___
  // |_|  |_|\___/  \_/  |_____|
  /////////////////////////////////

  public function getPossibleMoves($maxMove = null, $maxMoveAttack = null)
  {
    if ($maxMove != null) {
      $this->movementRadius = $maxMove;
    }

    if ($maxMoveAttack != null) {
      $this->movementAndAttackRadius = $maxMoveAttack;
    }

    return Board::getReachableCells($this);
  }

  public function moveTo($cell)
  {
    $this->setX($cell['x']);
    $this->setY($cell['y']);
  }

  //////////////////////////////////////////
  //    _  _____ _____  _    ____ _  __
  //    / \|_   _|_   _|/ \  / ___| |/ /
  //   / _ \ | |   | | / _ \| |   | ' /
  //  / ___ \| |   | |/ ___ \ |___| . \
  // /_/   \_\_|   |_/_/   \_\____|_|\_\
  //////////////////////////////////////////

  // Could have used getMustSeeToAttack but more readable that way
  public function mustSeeToAttack()
  {
    return $this->mustSeeToAttack;
  }

  public function getTargetableUnits($overrideMoves = null)
  {
    return Board::getTargetableCells($this, null, $overrideMoves);
  }

  public function getHits($type, $nb)
  {
    $map = [\INFANTRY => \DICE_INFANTRY, \ARMOR => \DICE_ARMOR];
    if (isset($map[$this->type]) && $type == $map[$this->type]) {
      return $nb;
    }

    if ($type == \DICE_GRENADE) {
      return $nb;
    }

    return 0;
  }

  // used to get additional hits from special power of unit (sniper, etc.)
  public function getHitsOnTarget($type, $nb)
  {
    return -1;
  }

  /*
   * return true if unit is dead
   */
  public function takeDamage($hits)
  {
    $hits = $hits >= $this->nUnits ? $this->nUnits : $hits;
    $this->incNUnits(-$hits);
    return $hits;
  }

  public function heal($heal)
  {
    $maxHeal = $this->maxUnits - $this->nUnits;
    $heal = $heal <= $maxHeal ? $heal : $maxHeal;
    $this->incNUnits($heal);
    return $heal;
  }

  public function isEliminated()
  {
    return $this->nUnits == 0;
  }

  public function cannotArmorOverrun()
  {
    return Board::cannotArmorOverrunCell(self::getPos());
  }
}
