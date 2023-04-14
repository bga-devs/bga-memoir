<?php
namespace M44\Units;

// not renamed to AntiTankEarlyWar to preserve backward compatibility
class AntiTank extends Infantry
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Anti Tank Gun (<1942)');
    $this->number = '2c';
    $this->movementAndAttackRadius = 0;
    $this->movementRadius = 2;
    $this->maxGrounds = 0; // unit with equipment cannot take ground
    $this->cannotBattleIfMoved = true;
    $this->desc = [
      clienttranslate('Fires like the infantry unit it equips'),
      clienttranslate('Stars hit on Armor'),
      //clienttranslate('May only move 1-2 or battle'),
      clienttranslate('May not Take Ground'),
    ];
    $this->applyPropertiesModifiers();
  }

  public function getHitsOnTarget($type, $nb, $target)
  {
    if ($target->getType() == ARMOR && $type == \DICE_STAR) {
      return $nb;
    }
    return -1; // to keep hits done on the unit (normal resolution)
  }
}
