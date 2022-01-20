<?php
namespace M44\Terrains;

class Road extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'road';
    $this->name = clienttranslate('Road');
    $this->landscape = 'country';
    $this->road = true;
  }
}
