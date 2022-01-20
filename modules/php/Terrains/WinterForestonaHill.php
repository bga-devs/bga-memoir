<?php
namespace M44\Terrains;

class WinterForestonaHill extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'whillforest';
    $this->name = clienttranslate('Winter Forest on a Hill');
    $this->landscape = 'winter';
    $this->vegetation = true;
    $this->elevation = true;
  }
}
