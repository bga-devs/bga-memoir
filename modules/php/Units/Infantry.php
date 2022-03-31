<?php
namespace M44\Units;

class Infantry extends AbstractUnit
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = INFANTRY;
    $this->number = 1;
    $this->statName = 'Inf';
    $this->name = clienttranslate('Infantry');
    $this->maxUnits = 4;
    $this->movementRadius = 2;
    $this->movementAndAttackRadius = 1;
    $this->attackPower = [3, 2, 1];
    // $this->attackPower = [3, 3, 3, 3, 3, 3];
    $this->mustSeeToAttack = true;
    $this->maxGrounds = 1;
  }
}
