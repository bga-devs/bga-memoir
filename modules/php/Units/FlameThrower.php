<?php
namespace M44\Units;

class FlameThrower extends Armor
{
  public function __construct($row)
  {
    $this->number = '13';
    $this->name = clienttranslate('Flame Thrower Tank');
    $this->maxMalus = -1;
    $this->desc[] = clienttranslate('Terrain battle dice reduction limited to 1 max in Close Assault');
    parent::__construct($row);
  }
}
