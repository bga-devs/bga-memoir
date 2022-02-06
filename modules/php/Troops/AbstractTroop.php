<?php
namespace M44\Troops;

use M44\Managers\Players;

class AbstractTroop extends \M44\Helpers\DB_Manager implements \JsonSerializable
{
  protected static $table = 'troops';
  protected static $primary = 'troop_id';

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
    ];
  }

  public function getId()
  {
    return $this->id;
  }
  public function getX()
  {
    return $this->x;
  }
  public function getY()
  {
    return $this->y;
  }
  public function getMaxUnits()
  {
    return $this->maxUnits;
  }

  public function getMoves()
  {
    return $this->moves;
  }

  public function getFights()
  {
    return $this->fights;
  }

  public function getActivationCard()
  {
    return $this->activationCard;
  }

  public function setActivationCard($cardId)
  {
    $this->activationCard = $cardId;
    self::DB()->update(['activation_card' => $this->activationCard], $this->id);
  }

  public function getActivationPlayer()
  {
    if ($this->getActivationCard() != null) {
      return Cards::get($this->getActivationCard())->getPlayer();
    } else {
      return null;
    }
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

  public function getExtraDatas($variable)
  {
    return $this->extraDatas[$variable] ?? null;
  }

  public function setExtraDatas($variable, $value)
  {
    $this->extraDatas[$variable] = $value;
    self::DB()->update(['extra_datas' => \addslashes(\json_encode($this->extraDatas))], $this->id);
  }
}
