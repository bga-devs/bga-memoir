<?php
namespace M44\Terrains;

class CurvedRailroad extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'railcurve';
    $this->name = clienttranslate('Curved Railroad');
    $this->landscape = 'country';
    $this->rail = true;
  }
}
