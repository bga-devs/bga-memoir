<?php
namespace M44\Terrains;

class Forest extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['woods', 'wforest']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Forests');
    $this->number = 3;

    $this->mustStop = true;
    $this->enteringCannotBattle = true;
    $this->blockLineOfSight = true;
    $this->defense = [INFANTRY => -1, ARMOR => -2];
    parent::__construct($row);
  }
}
