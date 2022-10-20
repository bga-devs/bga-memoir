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
    return is_null($scenario) ? null : $scenario['meta_data']['scenario_id'];
  }

  public function getMode()
  {
    $scenario = self::get();
    return is_null($scenario) ? null : $scenario['board']['type'];
  }

  public function getTopTeam()
  {
    $scenario = self::get();
    return is_null($scenario) ? null : $scenario['game_info']['side_player1'];
  }

  public function getOptions()
  {
    $scenario = self::get();
    return is_null($scenario) ? null : $scenario['game_info']['options'] ?? [];
  }

  /**
   * Load a scenario from a file and store it into a global
   */
  function loadId($id)
  {
    require_once dirname(__FILE__) . '/Scenarios/list.inc.php';
    $dir = 'Scenarios';

    // Add FromTheFront scenarios
    if (!isset($scenariosMap[$id])) {
      require_once dirname(__FILE__) . '/FromTheFront/list.inc.php';
      foreach ($fromTheFront as $name) {
        $scenarId = (int) explode('-', $name)[0];
        $scenariosMap[$scenarId] = $name;
      }

      if (!isset($scenariosMap[$id])) {
        throw new \BgaVisibleSystemException('Invalid scenario id');
      }
      $dir = 'FromTheFront';
    }

    $name = $scenariosMap[$id];
    $scenarios = [];
    require_once dirname(__FILE__) . '/' . $dir . '/' . $name . '.php';

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
}

function uc(&$str)
{
  $str = mb_strtoupper($str);
}
