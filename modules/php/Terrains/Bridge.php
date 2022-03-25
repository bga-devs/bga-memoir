<?php
namespace M44\Terrains;

class Bridge extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['bridge', 'pbridge', 'railbridge', 'wbridge', 'wrailbridge']) &&
      (!isset($hex['behavior']) || !in_array($hex['behavior'], ['BRIDGE_SECTION']));
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Bridge');
    $this->number = 9;
    $this->isBridge = true;
    $this->deltaAngle = 1;

    parent::__construct($row);
  }
}
