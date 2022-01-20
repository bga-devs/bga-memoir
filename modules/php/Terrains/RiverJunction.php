<?php
namespace M44\Terrains;

class RiverJunction extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'riverY';
    $this->name = clienttranslate('River Junction');
    $this->landscape = 'country';
    $this->water = true;
  }
}
