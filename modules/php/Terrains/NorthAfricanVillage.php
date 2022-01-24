<?php
namespace M44\Terrains;

class NorthAfricanVillage extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'bled';
    $this->name = clienttranslate('North-African Village');
    $this->landscape = 'sand';
    $this->buildings = true;
  }
}
