<?php
namespace M44\Terrains;

class WinterRoadCrossing extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'wroadX';
    $this->name = clienttranslate('Winter Road Crossing');
    $this->landscape = 'winter';
    $this->road = true;
  }
}
