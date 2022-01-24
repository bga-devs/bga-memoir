<?php
namespace M44\Terrains;

class RoadForkRight extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'roadFR';
    $this->name = clienttranslate('Road Fork - Right');
    $this->landscape = 'country';
    $this->road = true;
  }
}
