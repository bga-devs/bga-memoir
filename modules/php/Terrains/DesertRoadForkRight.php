<?php
namespace M44\Terrains;

class DesertRoadForkRight extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'droadFR';
    $this->name = clienttranslate('Desert Road Fork - Right');
    $this->landscape = 'sand';
    $this->road = true;
  }
}
