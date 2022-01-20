<?php
namespace M44\Terrains;

class DesertHill extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'dhill';
    $this->name = clienttranslate('Desert Hill');
    $this->landscape = 'sand';
    $this->elevation = true;
  }
}
