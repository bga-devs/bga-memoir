<?php
namespace M44\Terrains;

class DesertAirfieldCenter extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'dairfieldX';
    $this->name = clienttranslate('Desert Airfield Center');
    $this->landscape = 'sand';
    $this->air = true;
  }
}
