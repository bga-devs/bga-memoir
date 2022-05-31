<?php
namespace M44\Terrains;
use M44\Board;

class Fortress extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['fortress']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Fortresses');
    $this->number = 24;
    $this->isImpassable = [ARMOR, \ARTILLERY];
    $this->isBlockingLineOfSight = true;
    $this->canIgnoreAllFlags = true;
    $this->defense = [\INFANTRY => -1, ARMOR => -2];
    parent::__construct($row);
  }
}
