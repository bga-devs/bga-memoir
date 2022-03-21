<?php
namespace M44\Terrains;
use M44\Board;

class HillRoad extends Road
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['hillroad', 'hillcurve']);
  }

  public function __construct($row)
  {
    parent::__construct($row);
    $this->isHill = true;
  }

  public function isBlockingLineOfSight($unit, $target, $path)
  {
    $c = Board::getHillComponents();
    $s = $unit->getPos();
    $t = $target;
    return $c[$this->x][$this->y] != $c[$s['x']][$s['y']] || $c[$this->x][$this->y] != $c[$t['x']][$t['y']];
  }
}
