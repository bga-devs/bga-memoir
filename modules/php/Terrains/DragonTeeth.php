<?php
namespace M44\Terrains;

class DragonTeeth extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['dragonteeth']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Dragon\'s Teeth');
    $this->number = 46;
    $this->mustStopWhenEntering = true;
    $this->isImpassable = [ARMOR, \ARTILLERY];

    parent::__construct($row);
  }
}
