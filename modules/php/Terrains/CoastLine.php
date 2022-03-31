<?php
namespace M44\Terrains;

class CoastLine extends Beach
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
