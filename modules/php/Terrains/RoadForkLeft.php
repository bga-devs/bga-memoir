<?php
namespace M44\Terrains;

class RoadForkLeft extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'roadFL';
    $this->name = clienttranslate('Road Fork - Left');
    $this->landscape = 'country';
    $this->road = true;
  }
}
