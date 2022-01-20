<?php
namespace M44\Terrains;

class Ravine extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'wravine';
    $this->name = clienttranslate('Ravine');
    $this->landscape = 'winter';
    $this->elevation = true;
  }
}
