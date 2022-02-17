<?php
namespace M44\Units;

use M44\Board;
use M44\Managers\Players;

class AbstractUnit extends \M44\Helpers\DB_Manager implements \JsonSerializable
{
  protected static $table = 'units';
  protected static $primary = 'unit_id';

  protected $id = null;
  protected $x = null;
  protected $y = null;
  protected $sections = null;
  protected $type = null;
  protected $nation = null;
  protected $nUnits = null;
  protected $badge = null;
  protected $datas = null;

  protected $name = null;
  protected $maxUnits = null;
  protected $movementRadius = null;
  protected $movementAndAttackRadius = null;
  protected $attackPower = [];
  protected $mustSeeToAttack = true;
  protected $activationCard = null;
  protected $moves = 0;
  protected $fights = 0;

  public function __construct($row)
  {
    if ($row != null) {
      $this->id = (int) $row['id'];
      $this->x = (int) $row['x'];
      $this->y = (int) $row['y'];
      $this->sections = $row['sections'];
      $this->nation = $row['nation'];
      $this->nUnits = $row['figures'];
      $this->badge = $row['badge'];
      $this->moves = $row['moves'];
      $this->fights = $row['fights'];
      $this->activationCard = $row['activation_card'];
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

  public function getId()
  {
    return $this->id;
  }
  public function getType()
  {
    return $this->type;
  }
  public function getX()
  {
    return $this->x;
  }
  public function getY()
  {
    return $this->y;
  }
  public function getPos()
  {
    return [
      'x' => $this->x,
      'y' => $this->y,
    ];
  }
  public function getMaxUnits()
  {
    return $this->maxUnits;
  }

  public function getFights()
  {
    return $this->fights;
  }

  public function getActivationCard()
  {
    return $this->activationCard;
  }

  public function activate($card, $onTheMove = false)
  {
    $cardId = is_int($card) ? $card : $card->getId();
    $this->activationCard = $cardId;
    self::DB()->update(['activation_card' => $this->activationCard], $this->id);
    $this->setExtraDatas('onTheMove', $onTheMove);
  }

  public function getActivationPlayer()
  {
    if ($this->getActivationCard() != null) {
      return Cards::get($this->getActivationCard())->getPlayer();
    } else {
      return null;
    }
  }

  public function getExtraDatas($variable)
  {
    return $this->extraDatas[$variable] ?? null;
  }

  public function setExtraDatas($variable, $value)
  {
    $this->extraDatas[$variable] = $value;
    self::DB()->update(['extra_datas' => \addslashes(\json_encode($this->extraDatas))], $this->id);
  }

  public function getNation()
  {
    return $this->nation;
  }

  public function isOpponent($unit)
  {
    $allies = ['fr', 'gb', 'us', 'ru', 'ch'];
    $a = in_array($this->nation, $allies);
    $b = in_array($unit->getNation(), $allies);
    return ($a && !$b) || (!$a && $b);
  }

  /////////////////////////////////
  //  __  __  _____     _______
  // |  \/  |/ _ \ \   / / ____|
  // | |\/| | | | \ \ / /|  _|
  // | |  | | |_| |\ V / | |___
  // |_|  |_|\___/  \_/  |_____|
  /////////////////////////////////

  public function getMovementRadius()
  {
    return $this->movementRadius;
  }

  public function getMovementAndAttackRadius()
  {
    return $this->movementAndAttackRadius;
  }

  public function getMoves()
  {
    return $this->moves;
  }

  public function setMoves($value)
  {
    $this->moves = $value;
    self::DB()->update(['moves' => $this->moves], $this->id);
  }

  public function incMoves($value)
  {
    $this->moves += $value;
    self::DB()->update(['moves' => $this->moves], $this->id);
  }

  public function getPossibleMoves()
  {
    return Board::getReachableCells($this);
  }

  public function moveTo($cell)
  {
    $this->x = $cell['x'];
    $this->y = $cell['y'];
    self::DB()->update(['x' => $cell['x'], 'y' => $cell['y']], $this->id);
  }

  //////////////////////////////////////////
  //    _  _____ _____  _    ____ _  __
  //    / \|_   _|_   _|/ \  / ___| |/ /
  //   / _ \ | |   | | / _ \| |   | ' /
  //  / ___ \| |   | |/ ___ \ |___| . \
  // /_/   \_\_|   |_/_/   \_\____|_|\_\
  //////////////////////////////////////////
  public function getAttackPower()
  {
    return $this->attackPower;
  }

  public function setFights($value)
  {
    $this->fights = $value;
    self::DB()->update(['fights' => $this->fights], $this->id);
  }

  public function incFights($value)
  {
    $this->fights += $value;
    self::DB()->update(['fights' => $this->fights], $this->id);
  }

  public function getTargetableUnits()
  {
    return Board::getTargetableCells($this);
  }

  public function mustSeeToAttack()
  {
    return $this->mustSeeToAttack;
  }

  public function getHits($dice)
  {
    $map = [\INFANTRY => \DICE_INFANTRY, \ARMOR => \DICE_ARMOR];
    $hits = $dice[$map[$this->type]] ?? 0;

    $hits += $dice[\DICE_GRENADE] ?? 0;

    return $hits;
  }

  public function decFigures($value)
  {
    $this->nUnits -= $value;
    self::DB()->update(['figures' => $this->nUnits], $this->id);
  }

  /*
   * return true if unit is dead
   */
  public function takeDamage($hits)
  {
    if ($hits > $this->nUnits) {
      $this->decFigures($this->nUnits);
      return true;
    } else {
      $this->decFigures($hits);
      return false;
    }
  }
}
