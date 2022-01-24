<?php
namespace M44\Terrains;

class AircraftCarrier extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'pcarrier';
    $this->name = clienttranslate('Aircraft Carrier');
    $this->transport = true;
    $this->water = true;
  }
}
