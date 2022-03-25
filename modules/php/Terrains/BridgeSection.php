<?php
namespace M44\Terrains;

use M44\Board;

class BridgeSection extends Bridge
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
      \ALL_UNITS => [2, 3, 4, 8, 9, 10],
    ];
    parent::__construct($row);
  }

}
