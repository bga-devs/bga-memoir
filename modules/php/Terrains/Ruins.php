<?php
namespace M44\Terrains;

class Ruins extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['wruins']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('City Ruins');
    $this->number = 45;

    $this->mustStopWhenEntering = true;
    $this->enteringCannotBattle = true;
    $this->isImpassable = [ARMOR, ARTILLERY];
    $this->canIgnoreOneFlag = true;
    $this->isBlockingLineOfSight = true;
    $this->defense = [INFANTRY => -1, ARMOR => -2];
    parent::__construct($row);
  }
}
