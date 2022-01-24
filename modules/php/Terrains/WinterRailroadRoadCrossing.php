<?php
namespace M44\Terrains;

class WinterRailroadRoadCrossing extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'wrailroad';
    $this->name = clienttranslate('Winter Railroad / Road Crossing');
    $this->landscape = 'winter';
    $this->road = true;
    $this->rail = true;
  }
}
