<?php
namespace M44\Terrains;

class WinterAirfield extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'wairfield';
    $this->name = clienttranslate('Winter Airfield');
    $this->landscape = 'winter';
    $this->air = true;
  }
}
