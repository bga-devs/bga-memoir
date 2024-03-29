<?php
namespace M44\Models;
use M44\Board;
use M44\Core\Notifications;
use M44\Managers\Units;
use M44\Scenario;

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
    'mustBeAdjacentToBattle',
    'mustStopWhenEntering',
    'mustStopMovingWhenEntering',
    'enteringCannotBattle',
    'leavingCannotBattle',
    'canIgnoreOneFlag',
    'canIgnoreAllFlags',
    'mustIgnoreAllFlags',
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
    'canRecover',
    'avoidIfPossible',
    // 'isBlockingWadi',

    'isHill',
    'isBunker',
    'isBeach',
    'isBridge',
    'canBeBlown',
    'oneMedalIfBlown',
    'isMountain',
    'isRoad',
    'isCave',
    'isRiver',
    'isRail',

    'defense',
    'offense',

    'blockedDirections',
    'linkedDirections',
  ];
  protected $blockedDirections = [];
  protected $linkedDirections = [];

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

  public function getOrientation()
  {
    return $this->orientation;
  }

  public function getProperty($prop, $unit)
  {
    if (!in_array($prop, $this->properties)) {
      throw new \BgaVisibleSystemException('Trying to access a non existing terrain property : ' . $prop);
    }

    $isBoolean = !in_array($prop, ['offense', 'defense']);
    $defaultValue = $isBoolean ? false : null;
    // Prop not set => default value
    if (!isset($this->$prop)) {
      return $defaultValue;
    }

    // Prop is not an array => just use the value directly
    if (!is_array($this->$prop)) {
      return $this->$prop;
    }
    // Is it an SIDE assoc array ?
    $t = $this->$prop;
    if (isset($t[$unit->getTeamId()])) {
      $t = $t[$unit->getTeamId()];

      if (!is_array($t)) {
        return $t;
      }
    }

    return $isBoolean ? in_array($unit->getType(), $t) : $t[$unit->getType()] ?? $defaultValue;
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

  public function onUnitLeaving($unit, $isRetreat, $cell, $sourceCell = null)
  {
  }

  public function onUnitEntering($unit, $isRetreat, $isTakeGround)
  {
  }

  public function onUnitEliminated($unit)
  {
  }

  public function getPossibleAttackActions($unit)
  {
    return [];
  }

  public function getPossibleMoveActions($unit)
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

  public function getNeighboursInDirections($unit, $directions)
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
    $angles = $directions[$unit->getType()] ?? ($directions[ALL_UNITS] ?? []);
    foreach ($angles as $angle) {
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
    return in_array(
      ['x' => $cell['x'], 'y' => $cell['y']],
      $this->getNeighboursInDirections($unit, $this->blockedDirections)
    );
  }

  public function isLinked($cell, $unit)
  {
    return in_array(
      ['x' => $cell['x'], 'y' => $cell['y']],
      $this->getNeighboursInDirections($unit, $this->linkedDirections)
    );
  }

  public function getSections($side)
  {
    $mode = Scenario::getMode();
    $flipped = ($side == Scenario::getTopTeam());
    $sections = Units::$sections[$mode];
    $sections_results = [];
    for ($i = 0; $i < 3; $i++) {
      if ($sections[$i] <= $this->getPos()['x'] && $this->getPos()['x'] <= $sections[$i + 1]) {
        $sections_results[] = $flipped ? 2 - $i : $i;
      }
    }
    return $sections_results;
  }
}
