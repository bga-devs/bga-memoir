<?php
namespace M44\Terrains;

class RailCrossing extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'railX';
    $this->name = clienttranslate('Rail Crossing');
    $this->landscape = 'country';
    $this->rail = true;
  }
}
