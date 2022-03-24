<?php
namespace M44\Terrains;

class Dam extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['dam']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Dams');
    $this->number = 20;
    $this->desc = [
      clienttranslate('No movement restrictions for Infantry'),
      clienttranslate('No combat restrictions'),
    ];
    $this->impassable = [ARMOR, \ARTILLERY];
    $this->canIgnoreOneFlag = true;
    $this->isBlockingLineOfSight = true;
    parent::__construct($row);
  }
}
