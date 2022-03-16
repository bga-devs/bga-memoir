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

  public function getMedalsWorth()
  {
    if ($this->getExtraDatas('behavior') == 'GERMAN_2VICTANK') {
      return 2;
    }
    return 1;
  }
}
