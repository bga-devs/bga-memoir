<?php
namespace M44\Terrains;

class FordableStream extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['river', 'riverFL', 'riverFR', 'riverY', 'curve', 'pond']) &&
      isset($hex['behavior']) &&
      $hex['behavior'] == 'FORDABLE_STREAM';
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Fordable rivers');
    $this->number = '61';
    $this->mustStopMovingWhenEntering = true;

    parent::__construct($row);
  }
}
