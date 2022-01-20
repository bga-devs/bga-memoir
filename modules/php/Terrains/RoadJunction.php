<?php
namespace M44\Terrains;

class RoadJunction extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'roadY';
    $this->name = clienttranslate('Road Junction');
    $this->landscape = 'country';
    $this->road = true;
  }
}
