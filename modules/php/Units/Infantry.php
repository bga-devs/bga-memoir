<?php
namespace M44\Units;

class Infantry extends AbstractUnit
{
  public function __construct($row)
  {
    $this->type = INFANTRY;
    $this->number = 1;
    $this->statName = 'Inf';
    $this->name = clienttranslate('Infantry');
    $this->maxUnits = 4;
    $this->movementRadius = 2;
    $this->movementAndAttackRadius = 1;
    $this->attackPower = [3, 2, 1];
    $this->mustSeeToAttack = true;
    $this->maxGrounds = 1;
    parent::__construct($row);
  }

  public function getAttackModifier($target)
  {
    if ($this->getBonusCloseAssault() == true && !$this->isWounded() && $target['d'] == 1) {
      return 1;
    }
    return 0;
  }
}
