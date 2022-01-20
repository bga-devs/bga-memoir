<?php
namespace M44\Terrains;

class PowerPlant extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'powerplant';
    $this->name = clienttranslate('Power Plant');
    $this->landscape = 'country';
    $this->landmark = true;
  }
}
