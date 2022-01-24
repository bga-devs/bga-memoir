<?php
namespace M44\Terrains;

class FactoryComplex extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'factory';
    $this->name = clienttranslate('Factory Complex');
    $this->landscape = 'country';
    $this->landmark = true;
  }
}
