<?php
namespace M44\Terrains;

class Sandbag extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['sand']) && !isset($hex['behavior']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('SandBags');
    $this->number = 10;
    $this->defense = [\INFANTRY => -1, ARMOR => -1];
    $this->canIgnoreOneFlag = true;
    parent::__construct($row);
  }

  public function onUnitLeaving($unit, $isRetreat)
  {
    $this->removeFromBoard();
  }

  public function onUnitEliminated($unit)
  {
    $this->removeFromBoard();
  }
}
