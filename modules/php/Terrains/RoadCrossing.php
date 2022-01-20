<?php
namespace M44\Terrains;

class RoadCrossing extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'roadX';
    $this->name = clienttranslate('Road Crossing');
    $this->landscape = 'country';
    $this->road = true;
  }
}
