<?php
namespace M44\Managers;
use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Helpers\Utils;
use M44\Board;
use M44\Scenario;

/**
 * Units
 */
class Units extends \M44\Helpers\Pieces
{
  protected static $table = 'units';
  protected static $prefix = 'unit_';
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
    'retreats',
    'grounds',
    'extra_datas',
  ];
  protected static $autoreshuffle = false;
  protected static function cast($row)
  {
    $row['unit_id'] = $row['id'];
    $mode = Scenario::getMode();
    $flipped = in_array($row['nation'], self::$nations[Scenario::getTopTeam()]);
    $sections = self::$sections[$mode];
    $row['sections'] = [];
    for ($i = 0; $i < 3; $i++) {
      if ($sections[$i] <= $row['x'] && $row['x'] <= $sections[$i + 1]) {
        $row['sections'][] = $flipped ? 2 - $i : $i;
      }
    }

    return self::getInstance($row['type'], $row['badge'], $row);
  }

  public function getInstance($type, $badge, $row = null)
  {
    $className = '\M44\Units\\' . (TROOP_CLASSES[$type . $badge] ?? TROOP_CLASSES[$type]);
    return new $className($row);
  }

  //////////////////////////////////
  //////////////////////////////////
  //////////// GETTERS /////////////
  //////////////////////////////////
  //////////////////////////////////
  public static $nations = [
    ALLIES => ['fr', 'gb', 'us', 'ru', 'ch'],
    AXIS => ['ger', 'jp', 'it'],
  ];

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
    OVERLORD_DECK => [0, 14, 21, 35], // TODO : handle subsections
  ];

  public static function addSectionClause(&$q, $section)
  {
    $mode = Scenario::getMode();
    $sections = self::$sections[$mode];
    $q = $q->where('x', '>=', $sections[$section])->where('x', '<=', $sections[$section + 1]);
  }

  public static function addAliveClause(&$q)
  {
    $q = $q->where('figures', '>', 0);
  }

  public static function addTeamClause(&$q, $side)
  {
    $q = $q->whereIn('nation', self::$nations[$side]);
  }

  public static function getOfTeam($side)
  {
    $query = self::getSelectQuery();
    self::addTeamClause($query, $side);
    self::addAliveClause($query);
    return $query->get();
  }

  public static function getInSection($side, $section)
  {
    $query = self::getSelectQuery();
    self::addTeamClause($query, $side);
    self::addSectionClause($query, $section);
    self::addAliveClause($query);
    return $query->get();
  }

  public static function getActivatedByCard($card)
  {
    $cardId = is_int($card) ? $card : $card->getId();
    $q = self::getSelectQuery()->where('activation_card', $cardId);

    self::addAliveClause($q);
    return $q->get();
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
    $units = [];
    foreach ($board['hexagons'] as &$hex) {
      if (isset($hex['unit'])) {
        if (isset($hex['unit']['behavior']) && isset(\TROOP_BADGE_MAPPING[$hex['unit']['behavior']])) {
          $hex['unit']['badge'] = \TROOP_BADGE_MAPPING[$hex['unit']['behavior']];
        }
        $data = self::getTypeAndNation($hex['unit']);
        $unit = self::getInstance($data['type'], $data['badge']);
        $data['figures'] = $unit->getMaxUnits();
        $data['location'] = 'board';
        $data['x'] = $hex['col'];
        $data['y'] = $hex['row'];

        foreach (Board::getTerrainsInCell($data) as $terrain) {
          if ($terrain->isBunker($data)) {
            $side = in_array($data['nation'], self::$nations[AXIS]) ? AXIS : ALLIES;
            $terrain->setExtraDatas('owner', $side);
          }
        }

        $units[] = $data;
      }
    }

    self::create($units);
  }

  public function reset()
  {
    self::DB()
      ->update(['activation_card' => null, 'moves' => 0, 'fights' => 0, 'retreats' => 0])
      ->run();
  }

  public function addInCell($unit, $cell)
  {
    $data = self::getTypeAndNation($unit);
    $unit = self::getInstance($data['type'], $data['badge']);
    $data['location'] = 'board';
    $data['x'] = $cell['x'];
    $data['y'] = $cell['y'];
    $data['figures'] = $unit->getMaxUnits();
    return self::singleCreate($data);
  }

  public function remove($unitId)
  {
    $unitId = is_int($unitId) ? $unitId : $unitId->getId();
    self::DB()->delete($unitId);
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
          'badge' => isset($unit['badge']) ? substr($unit['badge'], 5) : 0,
        ];
      }
    }

    return null;
  }
}
