<?php
namespace M44\Terrains;

class RailForkRight extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'railFR';
    $this->name = clienttranslate('Rail Fork - Right');
    $this->landscape = 'country';
    $this->rail = true;
  }
}
