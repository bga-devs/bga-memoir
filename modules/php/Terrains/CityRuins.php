<?php
namespace M44\Terrains;

class CityRuins extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'wruins';
    $this->name = clienttranslate('City Ruins');
    $this->landscape = 'winter';
    $this->landmark = true;
  }
}
