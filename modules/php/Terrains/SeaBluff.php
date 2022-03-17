<?php
namespace M44\Terrains;
use M44\Board;

class SeaBluff extends Hill
{
  public static function isTileOfType($hex)
  {
    return $hex['name'] == 'hills' && isset($hex['behavior']) && $hex['behavior'] == '????';
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Sea Bluffs');
    $this->number = 6;
    $this->isHill = true;

    parent::__construct($row);
  }

  public function getLeavingDeplacementCost($unit, $source, $target, $d, $takeGround)
  {
    if (Board::isBeachCell($target)) {
      return $unit->getType() == \INFANTRY ? 2 : \INFINITY;
    } else {
      return 1;
    }
  }

  public function getEnteringDeplacementCost($unit, $source, $target, $d, $takeGround)
  {
    if (Board::isBeachCell($source)) {
      return $unit->getType() == \INFANTRY ? 2 : \INFINITY;
    } else {
      return 1;
    }
  }
}
