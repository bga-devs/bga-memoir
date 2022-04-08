<?php
namespace M44\Terrains;
use M44\Board;

class River extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['river', 'riverFL', 'riverFR', 'riverY', 'curve', 'pond', 'pmouth']) &&
      (!isset($hex['behavior']) || $hex['behavior'] == 'WIDE_RIVER');
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Rivers & Waterways');
    $this->number = 8;
    $this->desc = [\clienttranslate('Impassable, except over bridges')];
    $this->isImpassable = -1;

    parent::__construct($row);
  }

  public function isImpassable($unit)
  {
    if (Board::isBridgeCell(['x' => $this->x, 'y' => $this->y])) {
      return false;
    }
    return true;
  }
}
