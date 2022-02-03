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
use M44\Managers\Troops;
use M44\Managers\Teams;

class ScenarioLoader
{
  /**
   * Load a scenario from a file and store it into a global
   */
  function load($id)
  {
    /*
    $content = file_get_contents(dirname(__FILE__) . '/../cenarios/' . $id . '.m44');
    $t = \json_decode($content, true);
*/
    require_once dirname(__FILE__) . '/Scenarios/list.inc.php';
    if (!isset($scenariosMap[$id])) {
      throw new BgaVisibleSystemException('Invalid scenario id');
    }
    $name = $scenariosMap[$id];
    $scenarios = [];
    require_once dirname(__FILE__) . '/Scenarios/' . $name . '.php';

    Globals::setScenario($scenarios[$id]);
  }

  /**
   * Setup the scenario stored into the global
   */
  function setupScenario($rematch = false)
  {
    $scenario = Globals::getScenario();
    if (empty($scenario)) {
      throw new BgaVisibleSystemException('No scenario loaded');
    }

    // Game mode : standard, breakthrouh, overlord
    $mode = $scenario['board']['type'];

    // Create Teams
    Teams::loadScenario($scenario, false);

    // Create cards
    Cards::loadScenario($scenario);

    // Create Terrains tiles
    Terrains::loadScenario($scenario);

    // Create Troops
    Troops::loadScenario($scenario);

    // Activate player
    $infos = $scenario['game_info'];
    $startingSide = $infos['side_' . \strtolower($infos['starting'])];
    Globals::setSideTurn($startingSide);
    Globals::setTurn(0);
    Game::get()->gamestate->jumpToState(\ST_PREPARE_TURN);
  }
}
