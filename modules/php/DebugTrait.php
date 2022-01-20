<?php
namespace M44;
use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Core\Preferences;
use M44\Helpers\Utils;
use M44\Managers\Cards;
use M44\Managers\Players;

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
}
