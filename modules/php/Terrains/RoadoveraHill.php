<?php
namespace M44\Terrains;

class RoadoveraHill extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'hillroad';
    $this->name = clienttranslate('Road over a Hill');
    $this->landscape = 'country';
    $this->elevation = true;
    $this->road = true;
  }
}
