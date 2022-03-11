<?php
namespace M44\Terrains;

class Village extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['buildings']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Towns & Villages');
    $this->number = 14;

    $this->mustStopWhenEntering = true;
    $this->enteringCannotBattle = true;
    $this->isBlockingLineOfSight = true;
    $this->defense = [INFANTRY => -1, ARMOR => -2];
    $this->offense = [ARMOR => -2];
    parent::__construct($row);
  }
}
