<?php
namespace M44\Models;

class Terrain extends \M44\Helpers\DB_Model
{
  protected $table = 'terrains';
  protected $primary = 'tile_id';
  protected $attributes = [
    'type' => ['type', 'str'],
    'id' => ['tile_id', 'int'],
    'tile' => 'tile',
    'x' => ['x', 'int'],
    'y' => ['y', 'int'],
    'orientation' => ['orientation', 'int'],
    'owner' => 'owner',
    'extraDatas' => ['extra_datas', 'obj'],
  ];

  protected $id = null;
  protected $x = null;
  protected $y = null;
  protected $tile = null;
  protected $orientation = null;
  // protected $extraDatas = [];

  /*
   * STATIC INFORMATIONS
   */
  protected $staticAttributes = ['type', 'number', 'name'];
  protected $number = null;
  protected $type = null;
  protected $name = '';

  /*
   * TERRAIN PROPERTIES
   */
  protected $properties = [
    'isImpassable',
    'mustBeAdjacentToEnter',
    'mustStopWhenEntering',
    'enteringCannotBattle',
    'enteringCannotTakeGround',
    'canIgnoreOneFlag',
    'isBlockingLineOfSight',
    'mustStopWhenLeaving',
    'isBlockingSandbag',
    'cantLeave',
    'isImpassableForRetreat',
    'cannotBattle',
    'cantTakeGround',

    'isHill',
    'isBunker',
    'isBeach',

    'defense',
    'offense',
  ];

  public function __construct($row)
  {
    if ($row != null) {
      parent::__construct($row);
      // TODO : use extra datas to update some property of the terrain, and mark the terrain as 'non standard' in this case
    }
  }

  public function jsonSerialize()
  {
    $datas = [
      'id' => $this->id,
      'number' => $this->number,
      'x' => $this->x,
      'y' => $this->y,
      'orientation' => $this->orientation,
      'tile' => $this->tile,
      // 'datas' => $this->extraDatas,
    ];

    return $datas;
  }

  public function getStaticUiData()
  {
    $t = array_merge($this->staticAttributes, $this->properties);
    $datas = [];
    foreach ($t as $prop) {
      if (isset($this->$prop)) {
        $datas[$prop] = $this->$prop;
      }
    }

    return $datas;
  }

  public function getProperty($prop, $unit)
  {
    if (!in_array($prop, $this->properties)) {
      throw new \BgaVisibleSystemException('Trying to access a non existing terrain property : ' . $prop);
    }

    $isBoolean = !in_array($prop, ['offense', 'defense']);
    $defaultValue = $isBoolean ? false : null;
    return isset($this->$prop)
      ? (is_array($this->$prop)
        ? ($isBoolean
          ? in_array($unit->getType(), $this->$prop)
          : $this->$prop[$unit->getType()] ?? $defaultValue)
        : $this->$prop)
      : $defaultValue;
  }

  public function __call($method, $args)
  {
    if (!in_array($method, $this->properties)) {
      return parent::__call($method, $args);
    }

    $unit = $args[0];
    return $this->getProperty($method, $unit);
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

  public function onAfterAttack($unit)
  {
  }

  public function isValidPath($unit, $cell, $path)
  {
    return true;
  }

  public function getLeavingDeplacementCost($unit, $source, $target, $d, $takeGround)
  {
    return 1;
  }

  public function getEnteringDeplacementCost($unit, $source, $target, $d, $takeGround)
  {
    return 1;
  }

  public function isOriginalOwner($unit)
  {
    return $this->owner == null || $unit->getTeamId() == $this->owner;
  }

  public function defense($unit)
  {
    return $this->isOriginalOwner($unit) ? $this->getProperty('defense', $unit) : null;
  }

  public function canIgnoreOneFlag($unit)
  {
    return $this->isOriginalOwner($unit) ? $this->getProperty('canIgnoreOneFlag', $unit) : false;
  }
}
