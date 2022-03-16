<?php
namespace M44\Terrains;
use M44\Board;

class Cliff extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return $hex['name'] == 'hills' && isset($hex['behavior']) && $hex['behavior'] == 'CLIFF';
    // return in_array($hex['name'], ['hills', 'cliff']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Cliffs');
    $this->number = 6;
    $this->isCliff = true;

    parent::__construct($row);
  }

  public function isCliff($unit)
  {
    if (Board::isBeach($unit->getPos())) {
      return true;
    }
    return false;
  }
}
