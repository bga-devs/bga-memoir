<?php
namespace M44\Terrains;

class WinterRoad extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'wroad';
    $this->name = clienttranslate('Winter Road');
    $this->landscape = 'winter';
    $this->road = true;
  }
}
