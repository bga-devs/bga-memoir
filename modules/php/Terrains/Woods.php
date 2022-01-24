<?php
namespace M44\Terrains;

class Woods extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'woods';
    $this->name = clienttranslate('Woods');
    $this->landscape = 'country';
    $this->vegetation = true;

    $this->mustStop = true;
    $this->enteringCannotBattle = true;
    $this->blockLineOfSight = true;
    $this->defense = [INFANTRY => -1, ARMOR => -2];
  }
}
