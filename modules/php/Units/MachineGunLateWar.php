<?php
namespace M44\Units;
use M44\Board;

class MachineGunLateWar extends Infantry
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->number = '7c';
    $this->name = clienttranslate('Machine Gun (>1942)');
    $this->movementRadius = 2;
    $this->movementAndAttackRadius = 1;
    $this->attackPower = [3, 2, 1]; // by default if not moved or like infantry
    $this->maxGrounds = 0; // unit with equipment cannot take ground
    $this->desc = [
      clienttranslate('Treated as Infantry for all purposes'),
      clienttranslate('when it move battle like infantry 3/2/1'),
      clienttranslate('May not Take Ground'),
      clienttranslate('In addition when it does not move, stars hit on Infantry'),
      ];
    $this->applyPropertiesModifiers();
  }

  public function getHitsOnTarget($type, $nb, $target)
  {
    if ($target->getType() == INFANTRY && $type == \DICE_STAR && $this->getMoves() == 0) { // if not moved
      return $nb;
    }
    return -1; // to keep hits done on the unit (normal resolution)
  }
  
}
