<?php
namespace M44\Terrains;

class Lake2sides extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'lakeA';
    $this->name = clienttranslate('Lake (2 sides)');
    $this->landscape = 'country';
    $this->water = true;
  }
}
