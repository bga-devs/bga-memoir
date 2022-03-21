<?php
namespace M44\Terrains;

use M44\Board;

class BridgeSection extends Bridge
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['bridge']) && isset($hex['behavior']) && $hex['behavior'] == 'BRIDGE_SECTION';
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Bridge Section');
    $this->number = '9b';
    $this->desc = [\clienttranslate('You can only enter from the enter/exit hex')];
    parent::__construct($row);
  }

  public function getLeavingDeplacementCost($unit, $source, $target, $d, $takeGround)
  {
    if (Board::isBridgeCell($target)) {
      return 1;
    } elseif (self::areCellsContiguous($source, $target)) {
      return 1;
    } else {
      return \INFINITY;
    }
  }

  public function getEnteringDeplacementCost($unit, $source, $target, $d, $takeGround)
  {
    // throw new \feException(print_r($this));
    if (Board::isBridgeCell($source)) {
      return 1;
    } elseif (self::areCellsContiguous($source, $target)) {
      return 1;
    } else {
      return \INFINITY;
    }
  }

  protected function areCellsContiguous($source, $target)
  {
    $orientationMap = [
      1 => [['x' => 1, 'y' => 1], ['x' => -1, 'y' => 1]],
      2 => [['x' => -1, 'y' => 1]],
      3 => [['x' => 2, 'y' => 0], ['x' => -1, 'y' => 1]],
      4 => [['x' => 2, 'y' => 0]],
      5 => [['x' => 2, 'y' => 0], ['x' => -1, 'y' => -1]],
      6 => [['x' => -1, 'y' => -1]],
    ];

    foreach ($orientationMap[$this->orientation] as $cell) {
      if ($target['x'] - $source['x'] == $cell['x'] && $target['y'] - $source['y'] == $cell['y']) {
        return true;
      }
      if ($target['x'] - $source['x'] == $cell['x'] * -1 && $target['y'] - $source['y'] == $cell['y'] * -1) {
        return true;
      }
    }
    return false;
  }
}
