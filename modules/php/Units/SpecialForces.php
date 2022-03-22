<?php
namespace M44\Units;

class SpecialForces extends Infantry
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Special Forces');
    $this->number = 2;
    $this->movementAndAttackRadius = 2;
  }
}
