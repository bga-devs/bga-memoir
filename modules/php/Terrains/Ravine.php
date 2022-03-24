<?php
namespace M44\Terrains;

class Ravine extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['wravine']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Ravine');
    $this->number = 50;
    $this->isImpassable = [ARMOR, \ARTILLERY];
    $this->canIgnoreOneFlag = true;

    parent::__construct($row);
  }
}
