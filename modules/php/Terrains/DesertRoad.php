<?php
namespace M44\Terrains;

class DesertRoad extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'droad';
    $this->name = clienttranslate('Desert Road');
    $this->landscape = 'sand';
    $this->road = true;
  }
}
