<?php
namespace M44\Terrains;

class Sandbag extends \M44\Models\Obstacle
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['sand']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('SandBags');
    $this->number = 10;
    $this->defense = [\INFANTRY => -1, ARMOR => -1];
    $this->ignore1Flag = true;
    parent::__construct($row);
  }

  public function onUnitLeaving($unit)
  {
    $this->removeFromBoard();
  }

  public function onUnitEliminated($unit)
  {
    $this->removeFromBoard();
  }
}
