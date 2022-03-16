<?php
namespace M44\Terrains;

class Beach extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return false;
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Beaches');
    $this->number = 1;
    $this->isBeach = true;
    // Maximum movement onto beaches is 2 hexes

    parent::__construct($row);
  }

  public function isValidPath($unit, $cell, $path)
  {
    $mustCheck = true;
    // If sand is under unit, only check constraint if unit already made at least 1 move
    if ($this->getX() == $unit->getX() && $this->getY() == $unit->getY()) {
      $mustCheck = $unit->getMoves() > 0;
    }

    return !$mustCheck || $unit->getMoves() + count($path) - 1 <= 2;
  }
}