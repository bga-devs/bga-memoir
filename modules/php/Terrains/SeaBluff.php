<?php
namespace M44\Terrains;
use M44\Board;

class SeaBluff extends Hill
{
  public static function isTileOfType($hex)
  {
    return $hex['name'] == 'hills' && isset($hex['behavior']) && $hex['behavior'] == 'BLUFF';
  }

  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Cliffs & Sea Bluffs');
    $this->number = 11;
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
