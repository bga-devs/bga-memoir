<?php
namespace M44\Units;

class Locomotive extends AbstractUnit
{
  public function __construct($row)
  {
    $this->type = LOCOMOTIVE;
    $this->number = 5;
    $this->statName = 'Train';
    $this->name = clienttranslate('Trains');
    $this->maxUnits = 4;
    $this->movementRadius = 3;
    $this->movementAndAttackRadius = 0;
    $this->attackPower = [];
    parent::__construct($row);
  }
}
