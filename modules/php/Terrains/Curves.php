<?php
namespace M44\Terrains;

class Curves extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'curve';
    $this->name = clienttranslate('Curves');
    $this->landscape = 'country';
    $this->water = true;
  }
}
