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
    $this->desc = [
      clienttranslate('Moving up from/down on beach is a 2 hex move for Infantry'),
      clienttranslate('Armor/Artillery may not move up/down from beach'),
      //clienttranslate('Cliffs only - Infantry may not Take Ground from the beach'),
      clienttranslate('Treat as normal hill from inland side for movement and battle'),
      clienttranslate('Treat as normal hill from both sides for retreat'),
    ];
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
