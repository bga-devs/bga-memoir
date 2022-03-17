<?php
namespace M44\Terrains;
use M44\Board;

class Cliff extends Hill
{
  public static function isTileOfType($hex)
  {
    return $hex['name'] == 'hills' && isset($hex['behavior']) && $hex['behavior'] == 'CLIFF';
  }

  public function __construct($row)
  {
    parent::__construct($row);
  }

  public function getEnteringDeplacementCost($unit, $source, $target, $d, $takeGround)
  {
    if ($takeGround && $unit->getType() == \INFANTRY && Board::isBeachCell($source)) {
      return \INFINITY;
    } elseif ($unit->getType() == \INFANTRY && Board::isBeachCell($source)) {
      return 2;
    } else {
      return parent::getEnteringDeplacementCost($unit, $source, $target, $d, $takeGround);
    }
  }

  public function getLeavingDeplacementCost($unit, $source, $target, $d, $takeGround)
  {
    if (Board::isBeachCell($target)) {
      return $unit->getType() == \INFANTRY ? 2 : \INFINITY;
    } else {
      return 1;
    }
  }
}
