<?php
namespace M44\Units;

class Cavalry extends Infantry
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Cavalry');
    $this->number = 8;
    $this->movementAndAttackRadius = 3;
    $this->movementRadius = 3;
    $this->attackPower = [2, 1];
    $this->canOverrun = true;
    $this->maxGrounds = 2;
    $this->desc[] = clienttranslate('Fire at 2, 1');
    $this->desc[] = clienttranslate(
      'On successfull close assault, may Take Ground and battle again like an Armor Overrun combat'
    );
    $this->applyPropertiesModifiers();
  }
}
