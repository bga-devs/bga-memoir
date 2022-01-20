<?php
namespace M44\Terrains;

class RiverForkLeft extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'riverFL';
    $this->name = clienttranslate('River Fork - Left');
    $this->landscape = 'country';
    $this->water = true;
  }
}
