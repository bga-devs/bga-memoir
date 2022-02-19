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

  function tp($pId, $unitId, $min, $max)
  {
    Globals::setRetreat([
      'unit' => $unitId,
      'min' => $min,
      'max' => $max,
    ]);
    $this->changeActivePlayerAndJumpTo((int) $pId, \ST_RETREAT);
  }
}
