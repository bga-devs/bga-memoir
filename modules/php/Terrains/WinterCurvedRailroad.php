<?php
namespace M44\Terrains;

class WinterCurvedRailroad extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'wrailcurve';
    $this->name = clienttranslate('Winter Curved Railroad');
    $this->landscape = 'winter';
    $this->rail = true;
  }
}
