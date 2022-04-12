<?php
namespace M44\Terrains;
use M44\Board;

class Mountain extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['mountain']) &&
      (!isset($hex['behavior']) || in_array($hex['behavior'], ['MOUNTAIN']));
  }

  public function __construct($row)
  {
    parent::__construct($row);

    $this->name = clienttranslate('Mountains');
    $this->number = 30;
    $this->isMountain = true;
    $this->desc = [
      clienttranslate(
        'Infantry may only move up or retreat onto a mountain from an adjacent hill or mountain. Infantry may only move down or retreat from a mountain to an adjacent hill or mountain'
      ),
      clienttranslate('Impassable by Armor & Artillery'),
      clienttranslate('Artillery set on a mountain fires at 3/3/2/2/1/1/1'),
      clienttranslate('Blocks line of sight (except from contiguous adjacent mountains)'),
    ];
    $this->isImpassable = [ARMOR, \ARTILLERY];
    $this->cantLeave = [\ARTILLERY];
  }

  public function getLeavingDeplacementCost($unit, $source, $target, $d, $takeGround)
  {
    return Board::isHillCell($target) || Board::isMountainCell($target) ? 1 : \INFINITY;
  }

  public function getEnteringDeplacementCost($unit, $source, $target, $d, $takeGround)
  {
    return Board::isHillCell($source) || Board::isMountainCell($source) ? 1 : \INFINITY;
  }

  public function isBlockingLineOfSight($unit, $target, $path)
  {
    $c = Board::getMountainComponents();
    $s = $unit->getPos();
    $t = $target;
    return $c[$this->x][$this->y] != $c[$s['x']][$s['y']] || $c[$this->x][$this->y] != $c[$t['x']][$t['y']];
  }

  public function isBlockingLineOfAttack()
  {
    return $this->getExtraDatas('properties')['isBlockingLineOfAttack'] ?? false;
  }

  public function defense($unit)
  {
    if ($unit->getType() == \ARTILLERY) {
      return 0;
    } else {
      $isMountain = Board::isMountainCell($unit->getPos());
      return $isMountain ? 0 : -2;
    }
  }
}
