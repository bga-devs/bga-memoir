<?php
namespace M44\Models;
use M44\Board;
use M44\Core\Notifications;

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
  protected $staticAttributes = ['type', 'number', 'name', 'desc'];
  protected $number = null;
  protected $type = null;
  protected $name = '';
  protected $desc = [];
  protected $deltaAngle = 2;

  /*
   * TERRAIN PROPERTIES
   */
  protected $properties = [
    'isImpassable',
    'mustBeAdjacentToEnter',
    'mustStopWhenEntering',
    'enteringCannotBattle',
    'leavingCannotBattle',
    'canIgnoreOneFlag',
    'canIgnoreAllFlags',
    'isBlockingLineOfSight',
    'isBlockingLineOfAttack',
    'mustStopWhenLeaving',
    'isBlockingSandbag',
    'cantLeave',
    'isImpassableForRetreat',
    'cannotBattle',
    'cantTakeGround',
    'hill317',
    'cannotArmorOverrun',

    'isHill',
    'isBunker',
    'isBeach',
    'isBridge',
    'isMountain',

    'defense',
    'offense',

    'blockedDirections',
  ];
  protected $blockedDirections = [];

  public function __construct($row)
  {
    if ($row != null) {
      parent::__construct($row);
      $prop = $this->getExtraDatas('properties') ?? [];
      foreach ($prop as $name => $value) {
        $this->$name = $value;
      }
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
    ];

    $prop = $this->getExtraDatas('properties') ?? [];
    if (!empty($prop)) {
      $datas['properties'] = $prop;
    }

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

  public function getPos()
  {
    return ['x' => $this->x, 'y' => $this->y];
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

  public function removeFromBoard()
  {
    Board::removeTerrain($this);
    Notifications::removeTerrain($this);
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

  public function getBlockedNeighbours($unit)
  {
    $orientationMap = [
      0 => ['x' => 2, 'y' => 0],
      2 => ['x' => 1, 'y' => -1],
      4 => ['x' => -1, 'y' => -1],
      6 => ['x' => -2, 'y' => 0],
      8 => ['x' => -1, 'y' => 1],
      10 => ['x' => 1, 'y' => 1],
    ];

    $cells = [];
    $blocked = $this->blockedDirections[$unit->getType()] ?? ($this->blockedDirections[ALL_UNITS] ?? []);
    foreach ($blocked as $angle) {
      // Check whether this corresponds to a real location
      $realOrientation = (($this->orientation - 1) * $this->deltaAngle + $angle) % 12;
      $delta = $orientationMap[$realOrientation] ?? null;
      if (is_null($delta)) {
        continue;
      }
      $cells[] = [
        'x' => $this->x + $delta['x'],
        'y' => $this->y + $delta['y'],
      ];
    }

    return $cells;
  }

  public function isBlocked($cell, $unit)
  {
    return in_array(['x' => $cell['x'], 'y' => $cell['y']], $this->getBlockedNeighbours($unit));
  }
}
