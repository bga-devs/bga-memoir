<?php
namespace M44\Terrains;

class Mountain extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'mountain';
    $this->name = clienttranslate('Mountain');
    $this->landscape = 'country';
    $this->elevation = true;
  }
}
