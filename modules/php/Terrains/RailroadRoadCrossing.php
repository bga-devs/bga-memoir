<?php
namespace M44\Terrains;

class RailroadRoadCrossing extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'railroad';
    $this->name = clienttranslate('Railroad / Road Crossing');
    $this->landscape = 'country';
    $this->road = true;
    $this->rail = true;
  }
}
