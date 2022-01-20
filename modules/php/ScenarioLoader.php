<?php
namespace M44;
use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Core\Preferences;
use M44\Helpers\Utils;
use M44\Managers\Cards;
use M44\Managers\Players;

class ScenarioLoader
{
  /**
   * Load a scenario from a file and store it into a global
   */
  function load($id)
  {
    $content = file_get_contents(dirname(__FILE__) . '/../scenarios/' . $id . '.m44');
    $t = \json_decode($content, true);
    Globals::setScenario($t);
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
    if($mode == STANDARD_DECK){
      Cards::initStandardDeck();
    }
  }
}
