<?php
namespace M44\Terrains;

class Pier extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'ppier';
    $this->name = clienttranslate('Pier');
    $this->landscape = 'sand';
    $this->manmade = true;
    $this->water = true;
  }
}
