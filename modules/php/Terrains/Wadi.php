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

  // public function mustBeAdjacentToBattle($unit)
  // {
  //   if ($unit->getType() == \ARTILLERY) {
  //     return false;
  //   }
  //
  //   foreach (Board::getTerrainsInCell($unit->getPos()) as $t) {
  //     if ($t instanceof \M44\Terrains\Wadi) {
  //       return false;
  //     }
  //   }
  //
  //   return $this->mustBeAdjacentToBattle;
  // }

  public function defense($unit)
  {
    foreach (Board::getTerrainsInCell($unit->getPos()) as $t) {
      if ($t instanceof \M44\Terrains\Wadi) {
        return null;
      }
    }
    return $this->getProperty('defense', $unit);
  }

  // public function isBlockingLineOfSight($unit, $target, $path)
  // {
  //   // if ($unit->getType() == \ARTILLERY) {
  //   //   return false;
  //   // }
  //
  //   foreach ($path as $cell) {
  //     foreach (Board::getTerrainsInCell($cell) as $terrain) {
  //       if (!$terrain instanceof \M44\Terrains\Wadi) {
  //         return true;
  //       }
  //     }
  //   }
  //
  //   return count($path) <= 2;
  // }

  public function isWadi($cell)
  {
    foreach (Board::getTerrainsInCell($cell) as $terrain) {
      if ($terrain instanceof \M44\Terrains\Wadi) {
        return true;
      }
    }
    return false;
  }

  // Check in case of movement of the unit, to check "canAttack"
  public function isBlockingLineOfSight($unit, $target, $path)
  {
    return self::isBlockingWadi($unit, $target, $path, false);
  }

  // used to check the Wadi case at computation time
  public function isBlockingWadi($unit, $target, $path, $force = true)
  {
    $startWadi = self::isWadi($unit->getPos());
    // added in case we start the movement, it's always true (as we check terrains of the unit)
    if ($force) {
      $startWadi = true;
    }
    $endWadi = self::isWadi($target);

    if (($startWadi && $endWadi) || (!$startWadi && !$endWadi)) {
      return false;
    } else {
      return count($path) > 2;
    }
  }
}
