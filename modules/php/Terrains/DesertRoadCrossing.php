<?php
namespace M44\Terrains;

class DesertRoadCrossing extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'droadX';
    $this->name = clienttranslate('Desert Road Crossing');
    $this->landscape = 'sand';
    $this->road = true;
  }
}
