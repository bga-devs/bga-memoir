<?php
namespace M44\Terrains;

class AirfieldCenter extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'airfieldX';
    $this->name = clienttranslate('Airfield Center');
    $this->landscape = 'country';
    $this->air = true;
  }
}
