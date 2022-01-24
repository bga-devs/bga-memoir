<?php
namespace M44\Terrains;

class DesertRoadForkLeft extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'droadFL';
    $this->name = clienttranslate('Desert Road Fork - Left');
    $this->landscape = 'sand';
    $this->road = true;
  }
}
