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
  function load($scenario)
  {
    Scenario::load($scenario);
    Scenario::setup();
  }

  function vt()
  {
    $this->actChooseCard([24]);
  }

  function tp()
  {
    $this->rollDice(Players::getCurrent(), 3, ['x' => 20, 'y' => 4]);
  }
}
