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

  public function getMode()
  {
    $scenario = self::get();
    return is_null($scenario) ? null : $scenario['board']['type'];
  }

  public function getTopSide()
  {
    $scenario = self::get();
    return is_null($scenario) ? null : $scenario['game_info']['side_player1'];
  }


  /**
   * Load a scenario from a file and store it into a global
   */
  function load($id)
  {
    require_once dirname(__FILE__) . '/Scenarios/list.inc.php';
    if (!isset($scenariosMap[$id])) {
      throw new BgaVisibleSystemException('Invalid scenario id');
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
  function setup($rematch = false)
  {
    $scenario = self::get();
    if (is_null($scenario)) {
      throw new BgaVisibleSystemException('No scenario loaded');
    }

    // Game mode : standard, breakthrouh, overlord
    $mode = self::getMode();

    // Create Teams
    Teams::loadScenario($scenario, $rematch);

    // Create cards
    Cards::loadScenario($scenario);

    // Create Terrains tiles
    Terrains::loadScenario($scenario);

    // Create Units
    Units::loadScenario($scenario);

    // Activate player
    $infos = $scenario['game_info'];
    $startingSide = $infos['side_' . \strtolower($infos['starting'])];
    Globals::setSideTurn($startingSide);
    Globals::setTurn(0);
    Game::get()->gamestate->jumpToState(\ST_PREPARE_TURN);
  }
}