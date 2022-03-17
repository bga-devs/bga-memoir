<?php
namespace M44\Terrains;
use M44\Board;

class SteepHill extends Hill
{
  public static function isTileOfType($hex)
  {
    return $hex['name'] == 'hills' && isset($hex['behavior']) && $hex['behavior'] == 'STEEP_HILL';
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Steep Hills');
    $this->number = 13;

    parent::__construct($row);
  }

  public function getEnteringDeplacementCost($unit, $source, $target, $d, $takeGround)
  {
    return Board::isHillCell($source)? 1 : 2;
  }
}
