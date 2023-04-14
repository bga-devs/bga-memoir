<?php
namespace M44;
use M44\Core\Game;
use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Core\Preferences;
use M44\Helpers\Utils;
use M44\Managers\Cards;
use M44\Managers\Players;
use M44\Managers\Terrains;
use M44\Managers\Units;
use M44\Managers\Teams;
use M44\Managers\Medals;
use M44\Managers\Tokens;
use M44\Board;

class Scenario extends \APP_DbObject
{
  protected static $scenario = null;
  public static function get()
  {
    if (self::$scenario == null) {
      // Not sure why but using Globals module truncate some part of the query and makes it unusable (at least the last time I checked)
      $scenario = self::getUniqueValueFromDB("SELECT value FROM global_variables WHERE name = 'scenario' LIMIT 1");
      self::$scenario = is_null($scenario) ? null : json_decode($scenario, true);
    }
    return self::$scenario;
  }

  public function getId()
  {
    $scenario = self::get();
    return is_null($scenario) ? null : $scenario['meta_data']['scenario_id'] ?? $scenario['meta_data']['id'];
  }

  public function getMode()
  {
    $scenario = self::get();
    return is_null($scenario) ? null : strtoupper($scenario['board']['type']);
  }

  public function getTopTeam()
  {
    $scenario = self::get();
    return is_null($scenario) ? null : \strtoupper($scenario['game_info']['side_player1']);
  }

  public function getDate()
  {
    $scenario = self::get();
    return is_null($scenario) ? null : \strtoupper($scenario['game_info']['date_begin']);
  }

  public function getOptions()
  {
    $scenario = self::get();
    return is_null($scenario) ? null : $scenario['game_info']['options'] ?? [];
  }

  function getFromTheFront($id)
  {
    require dirname(__FILE__) . '/FromTheFront/list.inc.php';
    if (!isset($fromTheFront[$id])) {
      throw new \BgaVisibleSystemException('Scenario doesn\'t exist or is not valid. Should not happen');
    }

    require dirname(__FILE__) . '/FromTheFront/' . $fromTheFront[$id]['file'];
    $scenarios[$id]['game_info']['side_player1'] = strtoupper($scenarios[$id]['game_info']['side_player1']);
    $scenarios[$id]['game_info']['side_player2'] = strtoupper($scenarios[$id]['game_info']['side_player2']);
    $scenarios[$id]['board']['face'] = strtoupper($scenarios[$id]['board']['face']);
    $scenarios[$id]['board']['type'] = strtoupper($scenarios[$id]['board']['type']);

    if (isset($scenarios[$id]['board']['hexagons']['item'])) {
      $scenarios[$id]['board']['hexagons'] = $scenarios[$id]['board']['hexagons']['item'];
    }
    if (isset($scenarios[$id]['board']['labels']['item'])) {
      $scenarios[$id]['board']['labels'] = $scenarios[$id]['board']['labels']['item'];
    }

    return $scenarios[$id];
  }

  function getPaginatedScenarios($query)
  {
    $query['page'] = $query['page'] ?? 1; // Keep it sync with frontend
    $query['pagination'] = $query['pagination'] ?? 20;
    $query['order'] = $query['order'] ?? ['id', 'inc'];

    // Get ordered scenario matching filters
    $valid = self::getMetadataFromTheFront($query);
    // Keep some of them according to pagination
    $pageNumber = $query['page'];
    $pageSize = $query['pagination'];
    $toGet = array_slice($valid, ($pageNumber - 1) * $pageSize, $pageSize);

    // Get the scenarios data
    $toSend = [];
    foreach ($toGet as $infos) {
      require dirname(__FILE__) . '/FromTheFront/' . $infos['file'];
      $scenario = $scenarios[$infos['id']];
      unset($scenario['board']);
      $scenario['id'] = $infos['id'];
      $scenario['name'] = $infos['name'];
      $toSend[] = $scenario;
    }

    return [
      'scenarios' => $toSend,
      'query' => $query,
      'currentPage' => $pageNumber,
      'numPages' => intdiv(count($valid), $pageSize),
    ];
  }

  function getMetadataFromTheFront($query)
  {
    require dirname(__FILE__) . '/FromTheFront/list.inc.php';
    $scenarios = [];
    foreach ($fromTheFront as $id => $infos) {
      $infos['id'] = $id;
      if (self::isSatisfyingFilters($infos, $query)) {
        $scenarios[] = $infos;
      }
    }

    $order = $query['order'];
    usort($scenarios, function ($s1, $s2) use ($order) {
      $o = $order[0];
      $s = $order[1] == 'inc' ? 1 : -1;
      if ($o == 'id') {
        return $s * ((int) $s1['id'] - $s2['id']);
      } elseif ($o == 'operation') {
        return $s * strcmp($s1[$o]['name'], $s2[$o]['name']);
      } else {
        return $s * strcmp($s1[$o], $s2[$o]);
      }
    });

    return $scenarios;
  }

  function isSatisfyingFilters($infos, $filters)
  {
    foreach (['type', 'id', 'front', 'author', 'name'] as $filter) {
      if (is_null($filters[$filter] ?? null)) {
        continue;
      }

      $f = strtoupper($filters[$filter]);
      $v = strtoupper($infos[$filter] ?? '');
      if (stripos($v, $f) === false) {
        return false;
      }
    }
    return true;
  }

  /**
   * Load a scenario from a file and store it into a global
   */
  function loadId($id)
  {
    require_once dirname(__FILE__) . '/Scenarios/list.inc.php';
    $scenarios = [];

    if (isset($scenariosMap[$id])) {
      $name = $scenariosMap[$id];
      require_once dirname(__FILE__) . '/Scenarios/' . $name . '.php';
    } else {
      // Add FromTheFront scenarios
      require dirname(__FILE__) . '/FromTheFront/list.inc.php';
      if (!isset($frontTheFront[$id])) {
        throw new \BgaVisibleSystemException('Invalid scenario id');
      }
      $file = $fromTheFront[$id]['file'];
      require_once dirname(__FILE__) . '/FromTheFront/' . $file;
    }

    // Enforce uppercase for starting side
    $scenario = $scenarios[$id];
    uc($scenario['game_info']['side_player1']);
    uc($scenario['game_info']['side_player2']);
    uc($scenario['board']['type']);
    uc($scenario['board']['face']);

    if (isset($scenario['board']['hexagons']['item'])) {
      $scenario['board']['hexagons'] = $scenario['board']['hexagons']['item'];
    }

    self::$scenario = $scenario;
    Globals::setScenario($scenario);
  }

  /**
   * Setup the scenario stored into the global
   */
  function setup($rematch = false, $forceRefresh = false)
  {
    $scenario = self::get();
    if (is_null($scenario)) {
      throw new \BgaVisibleSystemException('No scenario loaded');
    }

    // Game mode : standard, breakthrouh, overlord
    $mode = self::getMode();

    // Init Globals
    $options = $scenario['game_info']['options'] ?? [];
    Globals::setBlitz($options['blitz_rules'] ?? false);
    Globals::setCommissar($options['russian_commissar_rule'] ?? '');
    Globals::setDesert($options['north_african_desert_rules'] ?? false);
    Globals::setItalyRoyalArmy($options['italy_royal_army'] ?? false);
    Globals::setItalyHighCommand($options['italy_high_command'] ?? false);
    Globals::setBritishCommand($options['british_commonwealth'] ?? false);
    Globals::setMarineCommand($options['gung_ho'] ?? false);
    Globals::setNightVisibility($options['night_visibility_rules'] ?? false ? 1 : \INFINITY);
    Globals::setEmptySectionMedals($options['empty_section_medals'] ?? null);
    // Init date to get Late >1942 or Early War for SWA
    Globals::setBeginDate($scenario['game_info']['date_begin'] ?? null);


    // Create Teams
    Teams::loadScenario($scenario, $rematch);

    // Create cards
    Cards::loadScenario($scenario);

    // Create Terrains tiles
    Terrains::loadScenario($scenario);
    Board::init();

    // Create Units
    Units::loadScenario($scenario);

    // Initialize medals
    Medals::loadScenario($scenario, $rematch);
    Tokens::loadScenario($scenario, $rematch);

    // Handle options
    if (isset(self::getOptions()['deck_reshuffling'])) {
      Globals::setDeckReshuffle(self::getOptions()['deck_reshuffling']);
      Globals::setDefaultWinner(null);
      if (self::getId() == 19) {
        Globals::setDefaultWinner(AXIS);
      }
    }

    Board::init();
    // Notify
    if ($rematch || $forceRefresh) {
      $datas = Game::get()->getAllDatas();
      unset($datas['prefs']);
      unset($datas['discard']);
      unset($datas['canceledNotifIds']);
      unset($datas['localPrefs']);
      Notifications::refreshInterface($datas);
    }

    // Init hands
    Cards::initHands();

    // Activate player
    $infos = $scenario['game_info'];
    $starting = mb_strtolower($infos['starting']);
    if (!in_array($starting, ['player1', 'player2'])) {
      throw new \BgaVisibleSystemException(
        '"starting = ' . $starting . '" field of scenario is not currently supported'
      );
    }
    $startingTeam = $infos['side_' . $starting];
    Globals::setTeamTurn($startingTeam);
    Globals::setTurn(0);

    Medals::checkBoardMedals();
  }

  function validateScenario($scenario)
  {
    // constants copied from globals
    $TROOP_BADGE_MAPPING = ['FRENCH_RESISTANCE' => 'badge3'];

    $board = $scenario['board'];

    if (!isset($board['hexagons'])) {
      return false;
    }

    // Overlord is not managed yet
    if ($board['type'] == 'OVERLORD') {
      return false;
    }

    $infos = $scenario['game_info'];
    $starting = mb_strtolower($infos['starting']);
    if (!in_array($starting, ['player1', 'player2'])) {
      return false;
    }

    // Teams check
    $info = $scenario['game_info'];
    if (
      !isset($info['side_player1']) ||
      !isset($info['side_player2']) ||
      !isset($info['cards_player1']) ||
      !isset($info['cards_player2'])
    ) {
      return false;
    }

    // Terrain check
    foreach ($board['hexagons'] as $hex) {
      $keys = ['terrain', 'rect_terrain', 'obstacle'];
      foreach ($keys as $key) {
        if (isset($hex[$key])) {
          $terrain = $hex[$key];
          $type = self::validateTerrain($terrain);
          if (
            $type === false &&
            // those are units not terrain
            !in_array($terrain['name'], ['pdestroyer', 'loco', 'wagon'])
          ) {
            return false;
          }
        }
      }

      // Unit validation
      if (isset($hex['unit'])) {
        if (isset($hex['unit']['behavior']) && isset($TROOP_BADGE_MAPPING[$hex['unit']['behavior']])) {
          $hex['unit']['badge'] = $TROOP_BADGE_MAPPING[$hex['unit']['behavior']];
        }
        if (self::validateUnit($hex['unit']) === false) {
          return false;
        }
      }

      // medal validation
      $tags = $hex['tags'] ?? [];
      foreach ($tags as $tag) {
        // Medal
        if (strpos($tag['name'], 'medal') === 0) {
          continue;
        }
        // Mines
        elseif (in_array($tag['behavior'] ?? null, ['MINE_FIELD'])) {
          continue; // Handle in terrains instead
        }
        // Camouflage tags
        elseif (in_array($tag['name'], ['tag14', 'tag15'])) {
          continue;
          // Exit markers
        } elseif (($tag['behavior'] ?? null) == 'EXIT_MARKER') {
          continue;
        }
        return false;
      }
    }
    return true;
  }

  function validateTerrain($hex)
  {
    // prettier-ignore
    if (
      (in_array($hex['name'], ['airfieldX', 'airfield', 'dairfieldX', 'dairfield', 'pairfield', 'pairfieldX', 'wairfield'])) || // airfield
      (in_array($hex['name'], ['barracks'])) || // barracks
      (in_array($hex['name'], ['pbeach', 'beach'])) || // beach
      (in_array($hex['name'], ['bridge', 'pbridge', 'railbridge', 'wbridge', 'wrailbridge']) && (!isset($hex['behavior']) || !in_array($hex['behavior'], ['BRIDGE_SECTION']))) || // bridge
      (in_array($hex['name'], ['bridge']) && isset($hex['behavior']) && $hex['behavior'] == 'BRIDGE_SECTION') || // bridge section
      (in_array($hex['name'], ['bunker'])) || // Bunker
      (in_array($hex['name'], ['cemetery'])) || // Cemetery
      (in_array($hex['name'], ['church'])) || // Church
      (in_array($hex['name'], ['hills', 'whill']) && isset($hex['behavior']) && $hex['behavior'] == 'CLIFF') || // cliff
      (in_array($hex['name'], ['coast', 'coastcurve'])) || // coastline
      (in_array($hex['name'], ['dam']))|| // Dam
      (in_array($hex['name'], ['dragonteeth'])) || // DragonTeeth
      (in_array($hex['name'], ['dridge'])) || //Erg
      (in_array($hex['name'], ['descarpment'])) || // Escarpment
      (in_array($hex['name'], ['factory', 'wfactory'])) || // factory Complex
      (in_array($hex['name'], ['casemate', 'wbunker', 'dbunker', 'pbunker'])) || // Field bunker
      (in_array($hex['name'], ['ford']) || (isset($hex['behavior']) && $hex['behavior'] == 'FORD')) || // Ford
      (in_array($hex['name'], ['river', 'riverFL', 'riverFR', 'riverY', 'curve', 'pond']) && isset($hex['behavior']) && $hex['behavior'] == 'FORDABLE_STREAM') || // FordableStream
      (in_array($hex['name'], ['woods', 'wforest'])) || // Forest
      (in_array($hex['name'], ['fortress'])) || // Fortress
      (in_array($hex['name'], ['wriver', 'wriverFR', 'wcurved'])) || // Frozen river
      (in_array($hex['name'], ['hedgehog'])) || // Hedgehog
      (in_array($hex['name'], ['hedgerow'])) || // Hedgerow
      (in_array($hex['name'], ['highground'])) || // High Ground
      (in_array($hex['name'], ['hills', 'whill', 'dhill']) && (!isset($hex['behavior']) || in_array($hex['behavior'], ['HILL', 'IMPASSABLE_HILL', 'IMPASSABLE_BLOCKING_HILL']))) || // Hill
      (in_array($hex['name'], ['pcave'])) || // HillCave
      (in_array($hex['name'], ['whillforest'])) || // HillForest
      (in_array($hex['name'], ['whillvillage'])) ||// HillVillage
      (in_array($hex['name'], ['phospital'])) || // Hospital
      (in_array($hex['name'], ['dcamp', 'pheadquarter'])) || // HQ
      (in_array($hex['name'], ['pjungle'])) || // Jungle
      (in_array($hex['name'], ['lakeA', 'lakeB', 'lakeC'])) || // lake
      (in_array($hex['name'], ['lighthouse'])) || // Lighthouse
      (in_array($hex['name'], ['marshes', 'wmarshes'])) || // Marshes
      (in_array($hex['name'], ['mountain']) && (!isset($hex['behavior']) || in_array($hex['behavior'], ['MOUNTAIN', 'IMPASSABLE_HILL', 'IMPASSABLE_BLOCKING_HILL']))) || // Mountain
      (in_array($hex['name'], ['pmcave'])) ||// MountainCave
      (in_array($hex['name'], ['oasis'])) || // Oasis
      (in_array($hex['name'], ['palmtrees'])) || // PalmForest
      (in_array($hex['name'], ['ppier'])) || // Pier
      (in_array($hex['name'], ['pontoon'])) || // pontoon
      (in_array($hex['name'], ['powerplant'])) || // Powerplant
      (in_array($hex['name'], ['camp'])) || // Prison camp
      (in_array($hex['name'], ['radar'])) || // RadarStation
      (in_array($hex['name'], ['rail', 'railcurve', 'railFL', 'railFR', 'railX', 'wrail', 'wrailcurve'])) || // Rail
      (in_array($hex['name'], ['railbridge'])) || // RailroadBridge
      (in_array($hex['name'], ['station'])) || // RailStation
      (in_array($hex['name'], ['wravine'])) || // ravine
      (in_array($hex['name'], ['price'])) || // Rice paddles
      (in_array($hex['name'], ['river', 'riverFL', 'riverFR', 'riverY', 'curve', 'pond', 'pmouth']) && (!isset($hex['behavior']) || $hex['behavior'] == 'WIDE_RIVER')) || // River
      (in_array($hex['name'], ['road', 'roadcurve', 'roadFL', 'roadFR', 'roadX', 'roadY', 'droad', 'droadX', 'droadcurve', 'droadFL', 'droadFR', 'wroad', 'wroadcurve', 'wroadFL', 'wroadFR', 'wroadX','wroadY',])) || // Road
      (in_array($hex['name'], ['roadblock', 'droadblock'])) || // RoadBlock
      (in_array($hex['name'], ['hillroad', 'hillcurve'])) || // roadhill
      (in_array($hex['name'], ['wruins'])) || // ruins
      (in_array($hex['name'], ['sand']) && !isset($hex['behavior'])) || // Sandbag
      ($hex['name'] == 'hills' && isset($hex['behavior']) && $hex['behavior'] == 'BLUFF') || // SeaBluff
      (in_array($hex['name'], ['sand']) && isset($hex['behavior']) && $hex['behavior'] == 'SEAWALL') || // SeaWall
      ($hex['name'] == 'hills' && isset($hex['behavior']) && $hex['behavior'] == 'STEEP_HILL') || // SteepHill
      (in_array($hex['name'], ['depot'])) || // supplydepot
      (in_array($hex['name'], ['wtrenches', 'ptrenches'])) || // Trenches
      (in_array($hex['name'], ['buildings', 'bled', 'wvillage', 'pvillage'])) || // Village
      (in_array($hex['name'], ['wadi', 'wcurve'])) || // Wadi
      (in_array($hex['name'], ['wire'])) // Wire
    ) {
      return true;
    }

    return false;
  }

  function validateUnit($unit)
  {
    $TROOP_CLASSES = [
      'inf2' => 'SpecialForces',
      'tank2' => 'EliteArmor',
      'inf' => 'Infantry',
      'tank' => 'Armor',
      'gun' => 'Artillery',
      'pdestroyer' => 'Destroyer',
      'loco' => 'Locomotive',
      'wagon' => 'Wagon',
      // type + badge number if non decorative
      'inf2_3' => 'FrenchResistance',
      'gun_5' => 'BigGun',
      'inf2_6' => 'CombatEngineer',
      'inf2_8' => 'CombatEngineer',
      'inf2_12' => 'CombatEngineer',
      'inf2_28' => 'CombatEngineer',
      'inf_6' => 'CombatEngineer',
      'inf_8' => 'CombatEngineer',
      'inf_12' => 'CombatEngineer',
      'inf_28' => 'CombatEngineer',
      'inf_26' => 'Sniper',
      'inf_27' => 'Sniper',
      'inf_29' => 'Cavalry',
      'inf2_30' => 'SkiTroop',
      'inf_30' => 'SkiTroop',
      'inf_37' => 'AntiTank',
      'inf_37lw' => 'AntiTankLateWar',
      'inf_41' => 'MortarEarlyWar',
      'inf_41lw' => 'MortarLateWar',
      'inf_45' => 'MachineGunEarlyWar',
      'inf_45lw' => 'MachineGunLateWar',
      'gun_35' => 'MobileArtillery',
      'gun_46' => 'HeavyAntiTankGun',
      'tank2_33' => 'FlameThrower',
      'tiger'=> 'Tiger',
    ];
    $name = $unit['name'];
    foreach (array_keys($TROOP_CLASSES) as $t) {
      if (stripos($name, $t) !== false) {
        return true;
      }
    }
    return false;
  }
}

function uc(&$str)
{
  $str = mb_strtoupper($str);
}
