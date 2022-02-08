<?php
namespace M44;
use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Core\Preferences;
use M44\Helpers\Utils;
use M44\Managers\Cards;
use M44\Managers\Players;
use M44\Board;

trait DebugTrait
{
  function test()
  {
    ScenarioLoader::load('test');
    ScenarioLoader::setupScenario();
  }

  function test2()
  {
    ScenarioLoader::load('SwordBeach');
    ScenarioLoader::setupScenario();
  }

  function test3()
  {
    ScenarioLoader::load('test2');
    ScenarioLoader::setupScenario();
  }

  function load($scenario)
  {
    ScenarioLoader::load($scenario);
    ScenarioLoader::setupScenario();
  }

  function vt()
  {
    $this->actOrderUnits([10, 11], []);
  }

  function tp()
  {
    var_dump(Board::getCellsInLine(['x' => 8, 'y' => 4], ['x' => 21, 'y' => 1]));
  }
}
