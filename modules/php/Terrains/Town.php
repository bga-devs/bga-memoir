<?php
namespace M44\Terrains;

class Town extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'buildings';
    $this->name = clienttranslate('Town');
    $this->landscape = 'country';
    $this->buildings = true;
  }
}
