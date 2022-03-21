<?php
namespace M44\Terrains;

class PrisonCamp extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['camp']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Prison camps');
    $this->number = 35;

    $this->mustStopWhenEntering = true;
    $this->enteringCannotBattle = true;
    $this->isBlockingLineOfSight = true;
    $this->defense = [INFANTRY => -1, ARMOR => -2];
    $this->offense = [ARMOR => -2];
    $this->canIgnoreOneFlag = true;
    parent::__construct($row);
  }
}
