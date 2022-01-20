<?php
namespace M44\Terrains;

class Wadi extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'wadi';
    $this->name = clienttranslate('Wadi');
    $this->landscape = 'sand';
    $this->elevation = true;
  }
}
