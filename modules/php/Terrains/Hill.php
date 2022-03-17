<?php
namespace M44\Terrains;
use M44\Board;

class Hill extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['hills']) && (!isset($hex['behavior']) || $hex['behavior'] == 'HILL');
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Hills');
    $this->number = 6;
    $this->isHill = true;

    parent::__construct($row);
  }

  public function isBlockingLineOfSight($unit, $target, $path)
  {
    $c = Board::getHillComponents();
    $s = $unit->getPos();
    $t = $target;
    return $c[$this->x][$this->y] != $c[$s['x']][$s['y']] || $c[$this->x][$this->y] != $c[$t['x']][$t['y']];
  }

  public function defense($unit)
  {
    if ($unit->getType() == \ARTILLERY) {
      return 0;
    } else {
      $isHill = Board::isHillCell($unit->getPos());
      return $isHill ? 0 : -1;
    }
  }
}
