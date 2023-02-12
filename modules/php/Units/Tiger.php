<?php
namespace M44\Units;

class Tiger extends Armor
{
  public function __construct($row)
  {
    $this->type = ARMOR;
    $this->number = '16';
    $this->statName = 'Armor';
    $this->name = clienttranslate('Tiger');
    $this->maxUnits = 1;
    parent::__construct($row);
  }
}