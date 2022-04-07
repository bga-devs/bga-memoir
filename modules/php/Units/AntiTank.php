<?php
namespace M44\Units;

class AntiTank extends Infantry
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Anti Tank Gun');
    $this->number = '2b';
    $this->movementAndAttackRadius = 0;
    $this->movementRadius = 2;
    $this->maxGrounds = 0; // unit with equipment cannot take ground
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
