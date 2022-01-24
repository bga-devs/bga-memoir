<?php
namespace M44\Terrains;

class Roadblock extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'roadblock';
    $this->name = clienttranslate('Roadblock');
    $this->landscape = 'country';
    $this->block = true;
    $this->road = true;
  }
}
