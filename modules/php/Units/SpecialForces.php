<?php
namespace M44\Units;

class SpecialForces extends Infantry
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Special Forces');
    $this->movementAndAttackRadius = 2;
  }
}
