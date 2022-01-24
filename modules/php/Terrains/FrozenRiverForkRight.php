<?php
namespace M44\Terrains;

class FrozenRiverForkRight extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'wriverFR';
    $this->name = clienttranslate('Frozen River Fork - Right');
    $this->landscape = 'winter';
    $this->water = true;
  }
}
