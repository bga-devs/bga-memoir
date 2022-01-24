<?php
namespace M44\Terrains;

class WinterRoadForkRight extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'wroadFR';
    $this->name = clienttranslate('Winter Road Fork - Right');
    $this->landscape = 'winter';
    $this->road = true;
  }
}
