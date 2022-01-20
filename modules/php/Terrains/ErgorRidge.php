<?php
namespace M44\Terrains;

class ErgorRidge extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'dridge';
    $this->name = clienttranslate('Erg or Ridge');
    $this->landscape = 'sand';
    $this->elevation = true;
  }
}
