<?php
namespace M44\Terrains;

class DesertRoadblock extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'droadblock';
    $this->name = clienttranslate('Desert Roadblock');
    $this->landscape = 'sand';
    $this->block = true;
  }
}
