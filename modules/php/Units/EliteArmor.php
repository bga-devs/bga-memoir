<?php
namespace M44\Units;

class EliteArmor extends Armor
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Elite Armor');
    $this->maxUnits = 4;
  }
}
