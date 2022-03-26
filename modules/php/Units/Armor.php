<?php
namespace M44\Units;

class Armor extends AbstractUnit
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = ARMOR;
    $this->number = '1b';
    $this->statName = 'Armor';
    $this->name = clienttranslate('Armor');
    $this->maxUnits = 3;
    $this->movementRadius = 3;
    $this->movementAndAttackRadius = 3;
    $this->attackPower = [3, 3, 3];
    $this->mustSeeToAttack = true;
    $this->maxGrounds = 2;
    $this->canOverrun = true;
  }
}
