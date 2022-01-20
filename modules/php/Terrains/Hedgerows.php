<?php
namespace M44\Terrains;

class Hedgerows extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'hedgerow';
    $this->name = clienttranslate('Hedgerows');
    $this->landscape = 'country';
    $this->vegetation = true;
  }
}
