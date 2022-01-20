<?php
namespace M44\Terrains;

class RiverForkRight extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'riverFR';
    $this->name = clienttranslate('River Fork - Right');
    $this->landscape = 'country';
    $this->water = true;
  }
}
