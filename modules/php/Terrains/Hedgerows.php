<?php
namespace M44\Terrains;

class Hedgerows extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'hedgerow';
    $this->name = clienttranslate('Hedgerows');
    $this->landscape = 'country';
    $this->vegetation = true;

    $this->mustStop = true;
    $this->enteringCannotBattle = true;
    $this->blockLineOfSight = true;
    $this->defense = [INFANTRY => -1, ARMOR => -2];
  }
}
