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
      if (($sections[$i] <= $row['x'] && $row['x'] <= $sections[$i + 1]) || $row['location'] == 'reserve') {
        $row['sections'][] = $flipped ? 2 - $i : $i;
      }
    }

    return self::getInstance($row['type'], $row['badge'], $row);
  }

  public static function getInstance($type, $badge, $row = null)
  {
    $className = '\M44\Units\\' . (TROOP_CLASSES[$type . '_' . $badge] ?? TROOP_CLASSES[$type]);
    return new $className($row);
  }

  //////////////////////////////////
  //////////////////////////////////
  //////////// GETTERS /////////////
  //////////////////////////////////
  //////////////////////////////////

  /**
   * getStaticUiData : return all units static datas
   */
  public static function getStaticUiData()
  {
    $data = [];
    foreach (TROOP_CLASSES as $type => $className) {
      $className = '\M44\Units\\' . $className;
      $unit = new $className(null);
      $data[$unit->getNumber()] = $unit->getStaticUiData();
    }
    return $data;
  }

  public static $nations = [
    ALLIES => ['fr', 'gb', 'us', 'ru', 'ch', 'brit'],
    AXIS => ['ger', 'jp', 'it'],
  ];

  public static $sections = [
    STANDARD_DECK => [0, 7, 17, 24],
    BREAKTHROUGH_DECK => [0, 7, 17, 24],
    OVERLORD_DECK => [0, 17, 33, 50], // TODO : handle subsections
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
    $q = $q->whereIn('nation', self::$nations[\strtoupper($side)]);
  }

  public static function addOnStagingAreaClause(&$q)
  {
    $q = $q->where('unit_location', 'reserve');
  }

  // units only on board not on reserve
  public static function getAllOrdered()
  {
    return self::getSelectQuery()
      ->orderBy([static::$prefix . 'state', 'ASC'])
      ->where('unit_location', 'board')
      ->get();
  }

  public static function getOfTeam($side)
  {
    $query = self::getSelectQuery();
    self::addTeamClause($query, $side);
    self::addAliveClause($query);
    return $query->get();
  }

  public static function getOfTeamOnReserve($side)
  {
    $query = self::getSelectQuery();
    self::addTeamClause($query, $side);
    self::addAliveClause($query);
    self::addOnStagingAreaClause($query);
    return $query->get(); 
  }

  public static function getInSection($side, $section)
  {
    $query = self::getSelectQuery();
    self::addTeamClause($query, $side);
    self::addSectionClause($query, $section);
    self::addAliveClause($query);
    if (Globals::isCampaign()) {
      $query = $query->orWhere('unit_location', 'reserve')->whereIn('nation', self::$nations[\strtoupper($side)]);
    }
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
  public static function loadScenario($scenario)
  {
    self::DB()
      ->delete()
      ->run();
    $board = $scenario['board'];
    $units = [];
    $isBlitz = $scenario['game_info']['options']['blitz_rules'] ?? false;
    $isItalyRoyalArmy = Globals::isItalyRoyalArmy();
    $isPartialBlitz = $scenario['game_info']['options']['partial_blitz_rules'] ?? ''; // Affect only armor movement
    $terrainUnits = ['pdestroyer', 'loco', 'wagon'];

    foreach ($board['hexagons'] as &$hex) {
      $data = null;

      if (isset($hex['unit'])) {
        // special case unit is tiger if tank elite german and nb units == 1
        // nbr_units not necessarly defined by default in any scenario
        if (isset($hex['unit']['nbr_units'])) {
          if ($hex['unit']['name'] == 'tank2ger' && ($hex['unit']['nbr_units']) == 1) {
            $hex['unit']['name'] = 'tigerger';
            $hex['unit']['badge'] = 'badge4';
          } elseif (isset($hex['unit']['behavior']) && isset(\TROOP_BADGE_MAPPING[$hex['unit']['behavior']])) {
            $hex['unit']['badge'] = \TROOP_BADGE_MAPPING[$hex['unit']['behavior']];
          }
        } elseif (isset($hex['unit']['behavior']) && isset(\TROOP_BADGE_MAPPING[$hex['unit']['behavior']])) {
          $hex['unit']['badge'] = \TROOP_BADGE_MAPPING[$hex['unit']['behavior']];
        }
        $data = self::getTypeAndNation($hex['unit']);
      } elseif (isset($hex['rect_terrain']) && in_array($hex['rect_terrain']['name'], $terrainUnits)) {
        $nation = $scenario['game_info']['side_player2'] == ALLIES ? 'us' : 'ger';
        $data = ['nation' => $nation, 'type' => $hex['rect_terrain']['name'], 'badge' => ''];
        if (isset($hex['rect_terrain']['orientation'])) {
          $data['extra_datas']['orientation'] = $hex['rect_terrain']['orientation'];
        }
      }

      if (is_null($data)) {
        continue;
      }
      // Late or Early war SWA condition
      //TODO get date and if >1942 Late War else EarlyWar
      $date = Globals::getBeginDate();
      $year = substr($date, 0, 4);
      if (isset($data['badge'])) {
        if (in_array($data['badge'], ['37', '41', '45']) && strval($year) > '1942') {
          $data['badge'] =  $data['badge'] . 'lw';
        }
      }


      //Create instance of the unit class
      $unit = self::getInstance($data['type'], $data['badge']);

      $data['figures'] = $unit->getMaxUnits();
      $data['location'] = 'board';
      $data['x'] = $hex['col'];
      $data['y'] = $hex['row'];
      $data['extra_datas'] = ['properties' => []];
      if (isset($hex['unit']['behavior'])) {
        $data['extra_datas']['behavior'] = $hex['unit']['behavior'];
      }

      if (isset($hex['rect_terrain']['orientation'])) {
        $data['extra_datas']['orientation'] = $hex['rect_terrain']['orientation'];
      }

      if (isset($hex['unit']['nbr_units'])) {
        $data['figures'] = $hex['unit']['nbr_units'];
        $data['extra_datas']['properties']['maxUnits'] = $hex['unit']['nbr_units'];
      }

      if (isset($hex['unit']['equipment'])) {
        $data['extra_datas']['equipment'] = $hex['unit']['equipment'];
      }

      $behavior = $hex['unit']['behavior'] ?? null;
      if (in_array($behavior, ['ARMOR_MOVE_TWO', 'ELITE_ARMOR_MOVE_TWO'])) {
        $data['extra_datas']['properties']['movementRadius'] = 2;
        $data['extra_datas']['properties']['movementAndAttackRadius'] = 2;
      }
      if (in_array($behavior, ['CAN_IGNORE_ONE_FLAG'])) {
        $data['extra_datas']['properties']['canIgnoreOneFlag'] = true;
      }


      if (
        $unit->getType() == ARMOR &&
        ((in_array($data['nation'], self::$nations[ALLIES]) && ($isPartialBlitz == ALLIES || $isBlitz)) ||
          (in_array($data['nation'], self::$nations[AXIS]) && $isPartialBlitz == AXIS) ||
          $isPartialBlitz == 'all')
      ) {
        $data['extra_datas']['properties']['movementRadius'] = 2;
        $data['extra_datas']['properties']['movementAndAttackRadius'] = 2;
      }

      if ($isItalyRoyalArmy && (TROOP_NATION_MAPPING[$data['badge'] ?? ''] ?? $data['nation']) == 'it') {
        // to exclude planes (later on)
        if ($unit->getType() == \INFANTRY || $unit->getType() == \ARMOR || $unit->getType() == \ARTILLERY) {
          $data['extra_datas']['properties']['retreatHex'] = 3;
        }
        if ($unit->getType() == \ARTILLERY) {
          $data['extra_datas']['properties']['canIgnoreOneFlag'] = true;
        }
      }

      if ($data['nation'] == 'jp') {
        if (
          $unit->getType() == \INFANTRY &&
          !($unit instanceof \M44\Units\Sniper)
        ) {
          $data['extra_datas']['properties']['mustIgnore1Flag'] = true;
          $data['extra_datas']['properties']['bonusCloseAssault'] = true;
          if (!$unit->isSWAEquipped()) { // if SWA (Early War) is equipped banzai does not apply (it apply for later war SWA)
            $data['extra_datas']['properties']['banzai'] = true;
          }
        }
      }

      if ($data['nation'] == 'brit') {
        // TODO : temporary fix because you cant add british command in the editor currently...
        Globals::setBritishCommand(true);
      }

      $units[] = $data;
    }
    self::create($units);
  }

  public static function reset()
  {
    self::DB()
      ->update(['activation_card' => null, 'moves' => 0, 'fights' => 0, 'retreats' => 0, 'grounds' => 0])
      ->run();

    // Reset road bonuses
    foreach (self::getAll() as $unit) {
      if (!$unit->stayedOnRoad()) {
        $unit->setExtraDatas('stayedOnRoad', null);
      }
      if ($unit->getRoadBonus() == 0) {
        $unit->setExtraDatas('roadBonus', null);
        $unit->setExtraDatas('movesOnTheRoad', null);
      }
      if ($unit->isStopped() == true) {
        $unit->setExtraDatas('stopped', false);
      }
      $unit->setExtraDatas('cannotBattle', false);
      $unit->setExtraDatas('onTheMove', false);
      $unit->setExtraDatas('canTakeGround', false);
      $unit->setExtraDatas('cannotArmorOverrun', false);
    }
  }

  public static function addInCell($unit, $cell, $onStageArea = false)
  {
    $data = self::getTypeAndNation($unit);
    $unit = self::getInstance($data['type'], $data['badge']);
    if ($onStageArea) {
      $data['location'] = 'reserve';
      $data['extra_datas']['isOnReserveStaging'] = true;
    } else {
      $data['location'] = 'board';
      $data['extra_datas']['properties'] = [];
    }
    $data['x'] = $cell['x'];
    $data['y'] = $cell['y'];
    $data['figures'] = $unit->getMaxUnits();    
    return self::singleCreate($data);
  }

  public static function moveFromReserveToBoard($unit)
  {
    if ($unit->isOnReserveStaging()) {
      $unit->setLocation('board');
      $unit->leaveStagingArea();
    }  
  }

  public static function remove($unitId)
  {
    $unitId = is_int($unitId) ? $unitId : $unitId->getId();
    self::DB()->delete($unitId);
  }

  ////////////////////////
  //////// Utils /////////
  ////////////////////////
  public static function getTypeAndNation($unit)
  {
    $name = $unit['name'];
    foreach (array_keys(TROOP_CLASSES) as $t) {
      if (stripos($name, $t) !== false) {
        $nation = substr($name, strlen($t));
        // $nation = TROOP_NATION_MAPPING[$unit['badge'] ?? ''] ?? $nation;
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
