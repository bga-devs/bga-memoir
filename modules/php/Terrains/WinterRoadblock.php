<?php
namespace M44\Terrains;

class WinterRoadblock extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'wroadblock';
    $this->name = clienttranslate('Winter Roadblock');
    $this->landscape = 'winter';
    $this->block = true;
    $this->road = true;
  }
}
