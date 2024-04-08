<?php
namespace M44\Terrains;

use M44\Core\Globals;

class RailStation extends Village
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['station']);
  }

  public function __construct($row)
  {
    parent::__construct($row);

    $this->name = clienttranslate('Railroad station');
    $this->number = 39;

    $this->mustStopWhenEntering = true;
    $this->enteringCannotBattle = true;
    $this->isBlockingLineOfSight = true;
    $this->defense = [INFANTRY => -1, ARMOR => -2];
    $this->offense = [ARMOR => -2];
    $this ->isRail = true;
  }

  function onUnitEntering($unit, $isRetreat, $isTakeGround)
  { // Train Reinforcement condition
    if(!is_null(Globals::getSupplyTrain()) && in_array($unit->getType(), [LOCOMOTIVE, WAGON]) && !$isRetreat) {
      $unit->setExtraDatas('trainReinforcement', true);
    }
  }
}
