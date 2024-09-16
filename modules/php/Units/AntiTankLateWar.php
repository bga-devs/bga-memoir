<?php
namespace M44\Units;

class AntiTankLateWar extends Infantry
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Anti-Tank Gun (>1942)');
    $this->number = '5c';
    $this->movementAndAttackRadius = 1;
    $this->movementRadius = 2;
    $this->maxGrounds = 0; // unit with equipment cannot take ground
    $this->applyPropertiesModifiers();
    $this->desc = [
      clienttranslate('Treated as Infantry for all purposes'),
      clienttranslate('when it moves, battle like infantry 3/2/1'),
      clienttranslate('May not Take Ground'),
      clienttranslate('In addition when it does not move, stars hit on Armor or Vehicle'),
    ];
  }

  public function getHitsOnTarget($type, $nb, $target)
  {
    if ($target->getType() == ARMOR && $type == \DICE_STAR && $this->getMoves() == 0) { // if not moved
      return $nb;
    }
    return -1; // to keep hits done on the unit (normal resolution)
  }
}
