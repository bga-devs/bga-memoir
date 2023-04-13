<?php
namespace M44\Units;
use M44\Board;

class MachineGunEarlyWar extends Infantry
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->number = '8c';
    $this->name = clienttranslate('Machine Gun (1939-42)');
    $this->movementRadius = 2;
    $this->movementAndAttackRadius = 0;
    $this->maxGrounds = 0; // unit with equipment cannot take ground
    $this->cannotBattleIfMoved = true;
    $this->banzai = false; // even if japanese infantry (FAQ)
    $this->isSWAEquipped = true;
    $this->desc = [
      clienttranslate('Fires like the infantry unit it equips'),
      clienttranslate('Stars hit on infantry'),
      //clienttranslate('May only move 1-2 or battle'),
      clienttranslate('May not Take Ground'),
    ];
    $this->applyPropertiesModifiers();
  }

  public function getHitsOnTarget($type, $nb, $target)
  {
    if ($target->getType() == INFANTRY && $type == \DICE_STAR) {
      return $nb;
    }
    return -1; // to keep hits done on the unit (normal resolution)
  }

}
