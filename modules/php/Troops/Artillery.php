<?php
namespace M44\Troops;

class Artillery extends AbstractTroop
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = ARTILLERY;
    $this->name = clienttranslate('Artillery');
    $this->maxUnits = 2;
    $this->movementRadius = 1;
    $this->movementAndAttackRadius = 0;
    $this->attackPower = [3, 3, 2, 2, 1, 1];
    $this->mustSeeToAttack = false;
  }
}
