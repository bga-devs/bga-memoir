<?php
namespace M44\Terrains;

use M44\Board;

class Wadi extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['wadi', 'wcurve']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Wadis & Gullies');
    $this->number = '44';
    $this->desc = [
      \clienttranslate('Side slopes impassable, both in and out'),
      clienttranslate('No movement restrictions through open ends'),
      clienttranslate('Infantry or Armor battling in or out of Wadi or Gully must be adjacent to target'),
      clienttranslate('Do not block line of sight. Unit in Wadi or Guli blocks line of sight as normal'),
    ];
    $this->defense = [INFANTRY => -1, ARMOR => -1, \ARTILLERY => -1];

    parent::__construct($row);
  }

  // TODO: Wadi tunnel effect

  // public function getLeavingDeplacementCost($unit, $source, $target, $d, $takeGround)
  // {
  //   if (Board::isBridgeCell($target)) {
  //     return 1;
  //   } elseif (self::areCellsContiguous($source, $target)) {
  //     return 1;
  //   } else {
  //     return \INFINITY;
  //   }
  // }
  //
  // public function getEnteringDeplacementCost($unit, $source, $target, $d, $takeGround)
  // {
  //   // throw new \feException(print_r($this));
  //   if (Board::isBridgeCell($source)) {
  //     return 1;
  //   } elseif (self::areCellsContiguous($source, $target)) {
  //     return 1;
  //   } else {
  //     return \INFINITY;
  //   }
  // }
  //
  // protected function areCellsContiguous($source, $target)
  // {
  //   $orientationMap = [
  //     1 => [['x' => 1, 'y' => 1], ['x' => -1, 'y' => 1]],
  //     2 => [['x' => -1, 'y' => 1]],
  //     3 => [['x' => 2, 'y' => 0], ['x' => -1, 'y' => 1]],
  //     4 => [['x' => 2, 'y' => 0]],
  //     5 => [['x' => 2, 'y' => 0], ['x' => -1, 'y' => -1]],
  //     6 => [['x' => -1, 'y' => -1]],
  //   ];
  //
  //   foreach ($orientationMap[$this->orientation] as $cell) {
  //     if ($target['x'] - $source['x'] == $cell['x'] && $target['y'] - $source['y'] == $cell['y']) {
  //       return true;
  //     }
  //     if ($target['x'] - $source['x'] == $cell['x'] * -1 && $target['y'] - $source['y'] == $cell['y'] * -1) {
  //       return true;
  //     }
  //   }
  //   return false;
  // }
}
