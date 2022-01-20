<?php
namespace M44\Terrains;

class WinterRoadJunction extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'wroadY';
    $this->name = clienttranslate('Winter Road Junction');
    $this->landscape = 'winter';
    $this->road = true;
  }
}
