<?php
namespace M44\Terrains;

class DesertAirfield extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'dairfield';
    $this->name = clienttranslate('Desert Airfield');
    $this->landscape = 'sand';
    $this->air = true;
  }
}
