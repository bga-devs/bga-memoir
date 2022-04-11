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
    if (!isset($scenariosMap[$id])) {
      throw new \BgaVisibleSystemException('Invalid scenario id');
    }
    $name = $scenariosMap[$id];
    $scenarios = [];
    require_once dirname(__FILE__) . '/Scenarios/' . $name . '.php';

    self::$scenario = $scenarios[$id];
    Globals::setScenario($scenarios[$id]);
  }

  /**
   * Setup the scenario stored into the global
   */
  function setup($rematch = false, $forceRefresh = false)
  {
    $scenario = self::get();
    if (is_null($scenario)) {
      throw new BgaVisibleSystemException('No scenario loaded');
    }

    // Game mode : standard, breakthrouh, overlord
    $mode = self::getMode();

    // Init Globals
    Globals::setBlitz($scenario['game_info']['options']['blitz_rules'] ?? false);
    Globals::setCommissar($scenario['game_info']['options']['russian_commissar_rule'] ?? '');
    Globals::setDesert($scenario['game_info']['options']['north_african_desert_rules'] ?? false);
    Globals::setItalyRoyalArmy($scenario['game_info']['options']['italy_royal_army'] ?? false);
    Globals::setItalyHighCommand($scenario['game_info']['options']['italy_high_command'] ?? false);
    Globals::setBritishCommand($scenario['game_info']['options']['british_commonwealth'] ?? false);
    Globals::setMarineCommand($scenario['game_info']['options']['gung_ho'] ?? false);

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
      unset($data['canceledNotifIds']);
      Notifications::refreshInterface($datas);
    }

    // Init hands
    Cards::initHands();

    // Activate player
    $infos = $scenario['game_info'];
    $startingTeam = $infos['side_' . \strtolower($infos['starting'])];
    Globals::setTeamTurn($startingTeam);
    Globals::setTurn(0);

    Medals::checkBoardMedals();
  }
}
