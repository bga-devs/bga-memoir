<?php
namespace M44\Terrains;

class HighGround extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'highground';
    $this->name = clienttranslate('High Ground');
    $this->landscape = 'country';
    $this->elevation = true;
  }
}
