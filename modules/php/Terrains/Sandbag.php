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

    parent::__construct($row);
  }

  public function onUnitLeaving($unit)
  {
    $this->removeFromBoard();
  }
}
