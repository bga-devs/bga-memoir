<?php
namespace M44\Terrains;

class BridgeSection extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['bridge']) && isset($hex['behavior']) && $hex['behavior'] == 'BRIDGE_SECTION';
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Bridge Section');
    $this->number = '9b';
    $this->desc = [\clienttranslate('You can only enter from the enter/exit hex')];
    $this->blockedDirections = [
      \ALL_UNITS => [0, 1, 5, 6, 7, 11],
    ];
    $this->isBridge = true;
    $this->deltaAngle = -1;
    parent::__construct($row);
  }
}
