<?php
namespace M44\Terrains;

class CountryAirfield extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'cairfield';
    $this->name = clienttranslate('Country Airfield');
    $this->landscape = 'country';
    $this->air = true;
  }
}
