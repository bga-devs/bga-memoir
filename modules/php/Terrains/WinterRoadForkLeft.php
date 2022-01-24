<?php
namespace M44\Terrains;

class WinterRoadForkLeft extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'wroadFL';
    $this->name = clienttranslate('Winter Road Fork - Left');
    $this->landscape = 'winter';
    $this->road = true;
  }
}
