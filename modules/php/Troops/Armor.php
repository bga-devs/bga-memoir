<?php
namespace M44\Troops;

class Armor extends AbstractTroop
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = ARMOR;
    $this->name = clienttranslate('Armor');
    $this->maxUnits = 3;
    $this->movementRadius = 3;
    $this->movementAndAttackRadius = 3;
    $this->attackPower = [3, 3, 3];
    $this->mustSeeToAttack = true;
  }
}