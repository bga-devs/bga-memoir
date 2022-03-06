<?php
namespace M44\Units;

use M44\Board;
use M44\Scenario;
use M44\Managers\Players;
use M44\Managers\Units;
use M44\Managers\Cards;

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
    'name',
    'maxUnits',
    'movementRadius',
    'movementAndAttackRadius',
    'attackPower',
    'mustSeeToAttack',
    'maxGrounds',
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
  protected $name = null;
  protected $maxUnits = null;
  protected $movementRadius = null;
  protected $movementAndAttackRadius = null;
  protected $attackPower = [];
  protected $mustSeeToAttack = true;
  protected $maxGrounds = 0;

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

  public function getPlayer()
  {
    if (in_array($this->nation, Units::$nations[AXIS])) {
      return Players::getSide(AXIS);
    } else {
      return Players::getSide(\ALLIES);
    }
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
    return in_array($this->nation, Units::$nations[Scenario::getTopSide()]) ? -1 : 1;
  }

  public function getActivationOCard()
  {
    return is_null($this->activationCard) ? null : Cards::get($this->activationCard);
  }

  public function getSection()
  {
    return $this->sections;
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

  public function getTargetableUnits()
  {
    return Board::getTargetableCells($this);
  }

  public function getHits($dice)
  {
    $map = [\INFANTRY => \DICE_INFANTRY, \ARMOR => \DICE_ARMOR];
    $hits = $dice[$map[$this->type]] ?? 0;

    $hits += $dice[\DICE_GRENADE] ?? 0;

    return $hits;
  }

  /*
   * return true if unit is dead
   */
  public function takeDamage($hits)
  {
    if ($hits >= $this->nUnits) {
      $this->setNUnits(0);
      Board::refreshUnits();
      return true;
    } else {
      $this->incNUnits(-$hits);
      return false;
    }
  }

  public function isEliminated()
  {
    return $this->figures == 0;
  }
}
