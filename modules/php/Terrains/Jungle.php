<?php
namespace M44\Terrains;
use M44\Board;

class Jungle extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['pjungle']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Jungles');
    $this->number = 57;
    $this->desc = [
      clienttranslate('Unit moving in must stop and may battle if unit starts its move in adjacent hex'),
      clienttranslate(
        'If Armor unit starts its move in adjacent hex and makes a successful combat against unit in a Jungle hex, it may Take Ground and do an Armor Overrun'
      ),
    ];
    $this->isBlockingLineOfSight = true;
    $this->mustStopWhenEntering = true;
    $this->defense = [INFANTRY => -1, ARMOR => -2];
    parent::__construct($row);
  }

  public function cannotBattle($unit, $moves)
  {
    $pos1 = $unit->getPos();
    $pos2 = $this->getPos();
    return $moves > 1 || abs($pos1['x'] - $pos2['x']) + 2 * abs($pos1['y'] - $pos2['y']) > 3;
  }

/*
  public function onUnitEntering($unit, $isRetreat, $isTakeGround)
  {
    if (!$isRetreat) {
      if ($unit->getMoves() != 1) {
        $this->setExtraDatas('cannotBattle', true);
        $unit->setMoves($unit->getMovementRadius());
      }
    }
  }
*/
}
