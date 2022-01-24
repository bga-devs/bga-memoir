<?php
namespace M44\Terrains;

class Lake3sidesandriver extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'lakeC';
    $this->name = clienttranslate('Lake (3 sides) and river');
    $this->landscape = 'country';
    $this->water = true;
  }
}
