<?php
namespace M44\Models;

class Terrain implements \JsonSerializable
{
  protected $id = null;
  protected $x = null;
  protected $y = null;
  protected $orientation = null;
  protected $datas = [];

  /*
   * STATIC INFORMATIONS
   */
  protected $type = null;
  protected $name = '';

  protected $impassable = false;
  protected $mustStop = false;
  protected $enteringCannotBattle = false;
  protected $ignore1Flag = false;
  protected $blockLineOfSight = false;
  protected $defense = [];

  protected $water = false;
  protected $road = false;
  protected $rail = false;

  // USELESS ?? CATEGORIES IN THE EDITOR
  protected $landscape = '';
  protected $vegetation = false;
  protected $elevation = false;
  protected $manmade = false;
  protected $buildings = false;
  protected $landmark = false;
  protected $air = false;

  public function __construct($row)
  {
    if ($row != null) {
      $this->id = $row['id'];
      $this->x = $row['x'];
      $this->y = $row['y'];
      $this->orientation = $row['orientation'];
      $this->datas = \json_decode($row['extra_datas'], true);
    }
  }

  public function jsonSerialize()
  {
    return [
      'id' => $this->id,
      'x' => $this->x,
      'y' => $this->y,
      'orientation' => $this->orientation,

      'type' => $this->type,
      'name' => $this->name,
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

  public function getName()
  {
    return $this->name;
  }

  public function getImpassable()
  {
    return $this->impassable;
  }

  public function mustStopWhenEntering()
  {
    return $this->mustStop;
  }
}
