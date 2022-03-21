<?php
namespace M44\Terrains;
use M44\Board;

class River extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['river', 'riverFL', 'riverFR', 'riverY', 'curve', 'pond', 'pmouth']);
    // &&
    //  (!isset($hex['behavior']) || !in_array($hex['behavior'], ['WIDE_RIVER']));
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Rivers & Waterways');
    $this->number = 8;

    parent::__construct($row);
  }

  public function isImpassable($unit)
  {
    $terrains = Board::getTerrainsInCell($this->x, $this->y);
    foreach ($terrains as $terrain) {
      if ($terrain->getType() == 'bridge' || $terrain->getType() == 'bridgesection') {
        return false;
      }
    }

    return true;
  }
}
