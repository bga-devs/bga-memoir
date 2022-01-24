<?php
namespace M44\Terrains;

class Escarpment extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'descarpment';
    $this->name = clienttranslate('Escarpment');
    $this->landscape = 'sand';
    $this->elevation = true;
  }
}
