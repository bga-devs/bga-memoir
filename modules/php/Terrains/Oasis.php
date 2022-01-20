<?php
namespace M44\Terrains;

class Oasis extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'oasis';
    $this->name = clienttranslate('Oasis');
    $this->landscape = 'sand';
    $this->vegetation = true;
    $this->water = true;
  }
}
