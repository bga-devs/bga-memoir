<?php
namespace M44\Terrains;

class Ford extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['ford']) || (isset($hex['behavior']) && $hex['behavior'] == 'FORD');
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Fords');
    $this->number = 41;
    $this->isBridge = true;
    $this->mustStopWhenEntering = true;
    $this->offense = [\INFANTRY => -1, ARMOR => -1, \ARTILLERY => -1];

    parent::__construct($row);
  }
}
