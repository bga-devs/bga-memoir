<?php
namespace M44\Units;

class EliteArmor extends Armor
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Elite Armor');
    $this->number = '2b';
    $this->maxUnits = 4;
    $this->applyPropertiesModifiers();
  }

  public function getMedalsWorth()
  {
    if ($this->getExtraDatas('behavior') == 'GERMAN_2VICTANK') {
      return 2;
    }
    return 1;
  }
}
