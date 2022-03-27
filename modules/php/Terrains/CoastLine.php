<?php
namespace M44\Terrains;

class CoastLine extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['coast', 'coastcurve']);
  }

  // TODO : check which rules
  public function __construct($row)
  {
    $this->name = clienttranslate('Coastlines');
    $this->number = 68;
    // $this->desc = [
    //   \clienttranslate('Maximum movement onto beaches is 2 hexes'),
    //   \clienttranslate('No combat restrictions'),
    //   \clienttranslate('Taking Ground and Armor Overrun remains possible'),
    // ];
    // $this->isBeach = true;

    parent::__construct($row);
  }

  // public function isValidPath($unit, $cell, $path)
  // {
  //   $mustCheck = true;
  //   // If sand is under unit, only check constraint if unit already made at least 1 move
  //   if ($this->getPos() == $unit->getPos()) {
  //     $mustCheck = $unit->getMoves() > 0;
  //   }
  //
  //   return !$mustCheck || $unit->getMoves() + count($path) - 1 <= 2;
  // }
}
