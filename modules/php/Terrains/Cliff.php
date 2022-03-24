<?php
namespace M44\Terrains;
use M44\Board;

class Cliff extends SeaBluff
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['hills', 'whill']) && isset($hex['behavior']) && $hex['behavior'] == 'CLIFF';
  }

  public function getEnteringDeplacementCost($unit, $source, $target, $d, $takeGround)
  {
    if ($takeGround && $unit->getType() == \INFANTRY && Board::isBeachCell($source)) {
      return \INFINITY;
    } else {
      return parent::getEnteringDeplacementCost($unit, $source, $target, $d, $takeGround);
    }
  }
}
