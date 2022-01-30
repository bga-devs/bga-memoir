<?php
namespace M44\Troops;

class Infantry extends AbstractTroop
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = INFANTRY;
    $this->name = clienttranslate('Infantry');
    $this->maxUnits = 4;
    $this->movementRadius = 2;
    $this->movementAndAttackRadius = 1;
    $this->attackPower = [3, 2, 1];
    $this->mustSeeToAttack = true;
  }
}