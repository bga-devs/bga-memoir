<?php
namespace M44\Terrains;

class FordableStream extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {

    return in_array($hex['name'], ['river', 'riverFL', 'riverFR', 'riverY', 'curve', 'pond', 'pmouth']) &&
      isset($hex['behavior']) && $hex['behavior'] == 'FORDABLE_STREAM';
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Fordable rivers');
    $this->number = '41b';
    $this->mustStopWhenEntering = true;
    $this->offense = [\INFANTRY => -1, ARMOR => -1, \ARTILLERY => -1];

    parent::__construct($row);
  }
}
