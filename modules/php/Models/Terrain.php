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
  protected $number = null;
  protected $type = null;
  protected $tile = null;
  protected $name = '';

  protected $impassable = false;
  protected $mustStop = false;
  protected $enteringCannotBattle = false;
  protected $ignore1Flag = false;
  protected $blockLineOfSight = false;
  protected $defense = null;
  protected $offense = null;
  protected $canSandbagBePlaced = true;

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
      $this->type = $row['type'];
      $this->tile = $row['tile'];
      $this->x = $row['x'];
      $this->y = $row['y'];
      $this->orientation = $row['orientation'];
      $this->datas = \json_decode($row['extra_datas'], true);
      // TODO : use extra datas to update some property of the terrain, and mark the terrain as 'non standard' in this case
    }
  }

  public function jsonSerialize()
  {
    return [
      'id' => $this->id,
      'x' => $this->x,
      'y' => $this->y,
      'orientation' => $this->orientation,

      'number' => $this->number,
      'tile' => $this->tile,
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

  public function getType()
  {
    return $this->type;
  }

  public function isImpassable($unit)
  {
    return is_bool($this->impassable) ? $this->impassable : $this->impassable[$unit->getType()] ?? false;
  }

  public function canSandbagBePlaced($unit)
  {
    if ($this->isImpassable($unit)) {
      return false;
    } else {
      return $this->canSandbagBePlaced;
    }
  }

  public function mustStopWhenEntering($unit)
  {
    return is_bool($this->mustStop) ? $this->mustStop : $this->mustStop[$unit->getType()] ?? false;
  }

  public function canIgnoreOneFlag($unit)
  {
    return is_bool($this->ignore1Flag) ? $this->ignore1Flag : $this->ignore1Flag[$unit->getType()] ?? false;
  }

  public function cannotAttackWhenEntering($unit)
  {
    return is_bool($this->enteringCannotBattle)
      ? $this->enteringCannotBattle
      : $this->enteringCannotBattle[$unit->getType()] ?? false;
  }

  public function isBlockingLineOfSight($unit)
  {
    return is_bool($this->blockLineOfSight)
      ? $this->blockLineOfSight
      : $this->blockLineOfSight[$unit->getType()] ?? false;
  }

  public function getDefense($unit)
  {
    return is_array($this->defense) ? $this->defense[$unit->getType()] ?? null : $this->defense;
  }

  public function getOffense($unit)
  {
    return is_array($this->offense) ? $this->offense[$unit->getType()] ?? null : $this->offense;
  }

  public function onUnitLeaving($unit, $isRetreat)
  {
  }

  public function onUnitEntering($unit, $isRetreat)
  {
  }

  public function onUnitEliminated($unit)
  {
  }

  public function getPossibleAttackActions($unit)
  {
    return [];
  }
}
