<?php
namespace M44\Terrains;
use M44\Board;

class Bridge extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['bridge', 'pbridge', 'wbridge']) &&
      (!isset($hex['behavior']) || !in_array($hex['behavior'], ['BRIDGE_SECTION']));
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Bridge');
    $this->number = 9;
    $this->isBridge = true;
    $this->deltaAngle = -1;

    parent::__construct($row);
  }

  public function isImpassable($unit)
  {
    // Unit coming from a river cannot cross as it is on a boat
    if (Board::isRiverCell($unit->getPos())) {
      return true;
    }

    return false;
  }
}
