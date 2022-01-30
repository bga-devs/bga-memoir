<?php
namespace M44\Troops;

class AbstractTroop implements \JsonSerializable
{
  protected $id = null;
  protected $x = null;
  protected $y = null;
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

  public function __construct($row)
  {
    if ($row != null) {
      $this->id = $row['id'];
      $this->x = $row['x'];
      $this->y = $row['y'];
      $this->nation = $row['nation'];
      $this->nUnits = $row['figures'];
      $this->badge = $row['badge'];
      $this->datas = \json_decode($row['extra_datas'], true);
    }
  }

  public function jsonSerialize()
  {
    return [
      'id' => $this->id,
      'x' => $this->x,
      'y' => $this->y,

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
}