<?php
namespace M44\Terrains;
use M44\Board;

class Hill extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['hills']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Hills');
    $this->number = 6;
    $this->isHill = true;

    parent::__construct($row);
  }

  public function isBlockingLineOfSight($unit, $path)
  {
    $hillComponents = Board::getHillComponents();
    return $hillComponents[$this->x][$this->y] != $hillComponents[$unit->getX()][$unit->getY()];
  }

  public function defense($unit)
  {
    if ($unit->getType() == \ARTILLERY) {
      return 0;
    } else {
      $isHill = Board::isHill($unit->getPos());
      return $isHill ? 0 : -1;
    }
  }
}