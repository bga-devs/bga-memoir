<?php
namespace M44\Terrains;

class Church extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['church']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Churches');
    $this->number = 19;

    $this->mustStopWhenEntering = true;
    $this->enteringCannotBattle = true;
    $this->isBlockingLineOfSight = true;
    $this->defense = [INFANTRY => -1, ARMOR => -2];
    $this->offense = [ARMOR => -2];
    $this->canIgnoreOneFlag = true;
    parent::__construct($row);
  }
}
