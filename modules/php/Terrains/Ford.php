<?php
namespace M44\Terrains;

class Ford extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'ford';
    $this->name = clienttranslate('Ford');
    $this->landscape = 'country';
    $this->water = true;
    $this->bridge = true;
  }
}
