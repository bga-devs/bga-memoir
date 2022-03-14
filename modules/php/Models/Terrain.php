<?php
namespace M44\Models;

class Terrain extends \M44\Helpers\DB_Model
{
  protected $table = 'terrains';
  protected $primary = 'tile_id';
  protected $attributes = [
    'id' => ['tile_id', 'int'],
    'tile' => 'tile',
    'x' => ['x', 'int'],
    'y' => ['y', 'int'],
    'orientation' => ['orientation', 'int'],
    'extraDatas' => ['extra_datas', 'obj'],
  ];

  protected $id = null;
  protected $x = null;
  protected $y = null;
  protected $tile = null;
  protected $orientation = null;
  protected $extraDatas = [];

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

    'isHill',
    'isBunker',

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
    return [
      'id' => $this->id,
      'x' => $this->x,
      'y' => $this->y,
      'orientation' => $this->orientation,

      'number' => $this->number,
      'tile' => $this->tile,
      'name' => $this->name,
      'datas' => $this->extraDatas,
    ];
  }

  public function __call($method, $args)
  {
    if (!in_array($method, $this->properties)) {
      return parent::__call($method, $args);
    }

    $unit = $args[0];
    $defaultValue = in_array($method, ['offense', 'defense']) ? null : false;
    return isset($this->$method)
      ? (is_array($this->$method)
        ? $this->$method[$unit->getType()] ?? $defaultValue
        : $this->$method)
      : $defaultValue;
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
