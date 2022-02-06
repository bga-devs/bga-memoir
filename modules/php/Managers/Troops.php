<?php
namespace M44\Managers;
use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Helpers\Utils;
use M44\Board;

/**
 * Troops
 */
class Troops extends \M44\Helpers\Pieces
{
  protected static $table = 'troops';
  protected static $prefix = 'troop_';
  protected static $customFields = [
    'x',
    'y',
    'type',
    'nation',
    'figures',
    'badge',
    'activation_card',
    'moves',
    'fights',
    'extra_datas',
  ];
  protected static $autoreshuffle = false;
  protected static function cast($row)
  {
    $mode = Board::getMode();
    $sections = self::$sections[$mode];
    $row['sections'] = [];
    for ($i = 0; $i < 3; $i++) {
      if ($sections[$i] <= $row['x'] && $row['x'] <= $sections[$i + 1]) {
        $row['sections'][] = $i;
      }
    }

    return self::getInstance($row['type'], $row['badge'], $row);
  }

  public function getInstance($type, $badge, $row = null)
  {
    $className = '\M44\Troops\\' . TROOP_CLASSES[$type];
    return new $className($row);
  }

  //////////////////////////////////
  //////////////////////////////////
  //////////// GETTERS /////////////
  //////////////////////////////////
  //////////////////////////////////

  /**
   * getUiData : return all terrain tiles
   */
  public static function getUiData()
  {
    return self::getAllOrdered()->ui();
  }

  public static $sections = [
    STANDARD_DECK => [0, 7, 17, 24],
    BREAKTHROUGH_DECK => [0, 7, 17, 24],
    OVERLORD_DECK => [], // TODO : handle subsections
  ];

  public static function addSectionClause(&$q, $section)
  {
    $mode = Board::getMode();
    $sections = self::$sections[$mode];
    $q = $q->where('x', '>=', $sections[$section])->where('x', '<=', $sections[$section + 1]);
  }

  public static function addTeamClause(&$q, $side)
  {
    $nations = [
      ALLIES => ['fr', 'gb', 'us', 'ru', 'ch'],
      AXIS => ['ger', 'jp', 'it'],
    ];
    $q = $q->whereIn('nation', $nations[$side]);
  }

  public static function getInSection($side, $section)
  {
    $query = self::getSelectQuery();
    self::addTeamClause($query, $side);
    self::addSectionClause($query, $section);
    return $query->get();
  }

  //////////////////////////////////
  //////////////////////////////////
  ///////////// SETTERS //////////////
  //////////////////////////////////
  //////////////////////////////////

  /**
   * Load a scenario
   */
  public function loadScenario($scenario)
  {
    self::DB()
      ->delete()
      ->run();
    $board = $scenario['board'];
    $troops = [];
    foreach ($board['hexagons'] as $hex) {
      if (isset($hex['unit'])) {
        $data = self::getTypeAndNation($hex['unit']);
        $troop = self::getInstance($data['type'], $data['badge']);
        $data['figures'] = $troop->getMaxUnits();
        $data['location'] = 'board';
        $data['x'] = $hex['col'];
        $data['y'] = $hex['row'];
        $troops[] = $data;
      }
    }

    self::create($troops);
  }

  ////////////////////////
  //////// Utils /////////
  ////////////////////////
  public function getTypeAndNation($unit)
  {
    $name = $unit['name'];
    foreach (array_keys(TROOP_CLASSES) as $t) {
      if (stripos($name, $t) !== false) {
        $nation = substr($name, strlen($t));
        return [
          'type' => $t,
          'nation' => $nation,
          'badge' => $unit['badge'] ?? null,
        ];
      }
    }

    return null;
  }
}
