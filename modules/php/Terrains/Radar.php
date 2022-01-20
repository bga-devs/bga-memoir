<?php
namespace M44\Terrains;

class Radar extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'radar';
    $this->name = clienttranslate('Radar');
    $this->landscape = 'country';
    $this->landmark = true;
  }
}
