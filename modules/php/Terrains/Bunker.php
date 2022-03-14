<?php
namespace M44\Terrains;
use M44\Board;

class Bunker extends \M44\Models\RectTerrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['bunker']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Bunkers');
    $this->number = 6; // TODO
    $this->isBunker = true;
    $this->isBlockingLineOfSight = true;
    $this->defense = [INFANTRY => -1, ARMOR => -2];

    parent::__construct($row);
  }

  // public function defense($unit)
  // {
  //   $defense = [INFANTRY => -1, ARMOR => -2];
  //
  //   if ($unit->getType() == \ARTILLERY) {
  //     return 0;
  //   } else {
  //     $isHill = Board::isHill($unit->getPos());
  //     return $isHill ? 0 : -1;
  //   }
  // }
}
