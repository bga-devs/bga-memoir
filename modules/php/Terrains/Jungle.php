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
    $this->defense = [INFANTRY => -1, ARMOR => -2];
    parent::__construct($row);
  }

  public function onUnitEntering($unit, $isRetreat)
  {
    if (!$isRetreat) {
      if ($unit->getMoves() != 1) {
        $this->setExtraDatas('cannotBattle', true);
        $unit->setMoves($unit->getMovementRadius());
      }
    }
  }
}
