<?php
namespace M44\Terrains;

class Lake3sides extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'lakeB';
    $this->name = clienttranslate('Lake (3 sides)');
    $this->landscape = 'country';
    $this->water = true;
  }
}
