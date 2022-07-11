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
      // clienttranslate('Do not block line of sight. Unit in Wadi or Guli blocks line of sight as normal'),
    ];
    $this->defense = [INFANTRY => -1, ARMOR => -1, \ARTILLERY => -1];
    // $this->mustBeAdjacentToBattle = true;
    $this->blockedDirections = [
      ALL_UNITS =>
        ($row['tile'] ?? null) == 'wcurve' ? [0, 1, 3, 4, 5, 6, 7, 8, 9, 11] : [1, 2, 3, 4, 5, 7, 8, 9, 10, 11],
    ];
    parent::__construct($row);
  }

  public function defense($unit)
  {
    foreach (Board::getTerrainsInCell($unit->getPos()) as $t) {
      if ($t instanceof \M44\Terrains\Wadi) {
        return null;
      }
    }
    return $this->getProperty('defense', $unit);
  }

  public function isWadi($cell)
  {
    foreach (Board::getTerrainsInCell($cell) as $terrain) {
      if ($terrain instanceof \M44\Terrains\Wadi) {
        return true;
      }
    }
    return false;
  }

  // used to check the Wadi case at computation time
  public function isBlockingWadi($unit, $target, $path, $cell, $force = true)
  {
    $startWadi = self::isWadi($cell);
    $endWadi = self::isWadi($target);

    if (($startWadi && $endWadi) || (!$startWadi && !$endWadi)) {
      return false;
    } else {
      return count($path) > 2;
    }
  }
}
