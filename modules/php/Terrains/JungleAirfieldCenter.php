<?php
namespace M44\Terrains;

class JungleAirfieldCenter extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'pairfieldX';
    $this->name = clienttranslate('Jungle Airfield Center');
    $this->landscape = 'jungle';
    $this->air = true;
  }
}
