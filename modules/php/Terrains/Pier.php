<?php
namespace M44\Terrains;
use M44\Board;

class Pier extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['ppier']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Piers');
    $this->number = 59;
    $this->desc = [
      \clienttranslate('Movement onto only allowed from Land or Beach, not from Ocean Hex'),
      clienttranslate('No combat restriction'),
    ];
    $this->linkedDirections = [
      ALL_UNITS => [5, 6],
    ];
    parent::__construct($row);
  }
}
