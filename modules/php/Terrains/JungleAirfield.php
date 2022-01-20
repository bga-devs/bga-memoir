<?php
namespace M44\Terrains;

class JungleAirfield extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'pairfield';
    $this->name = clienttranslate('Jungle Airfield');
    $this->landscape = 'jungle';
    $this->air = true;
  }
}
