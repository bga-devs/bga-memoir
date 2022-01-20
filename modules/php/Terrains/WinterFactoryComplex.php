<?php
namespace M44\Terrains;

class WinterFactoryComplex extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'wfactory';
    $this->name = clienttranslate('Winter Factory Complex');
    $this->landscape = 'winter';
    $this->landmark = true;
  }
}
