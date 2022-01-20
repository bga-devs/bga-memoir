<?php
namespace M44\Terrains;

class River extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'river';
    $this->name = clienttranslate('River');
    $this->landscape = 'country';
    $this->water = true;
  }
}
