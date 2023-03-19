<?php
namespace M44\Units;

class Tiger extends EliteArmor
{
  public function __construct($row)
  {
    parent::__construct($row);
    //$this->type = ARMOR;
    $this->number = 16;
    $this->statName = 'Armor';
    $this->name = clienttranslate('Tiger');
    $this->maxUnits = 1;
    $this->applyPropertiesModifiers();
  }

  public function getAttackModifier($target)
  { 
    return ($this-> getExtraDatas('behavior') == 'IS_WITTMANN' && $this-> getMoves() == 0 && $this-> getGrounds() == 0) ? 1 : 0;
  }

}
