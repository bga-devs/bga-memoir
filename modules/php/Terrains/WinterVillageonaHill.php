<?php
namespace M44\Terrains;

class WinterVillageonaHill extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'whillvillage';
    $this->name = clienttranslate('Winter Village on a Hill');
    $this->landscape = 'winter';
    $this->elevation = true;
    $this->buildings = true;
  }
}
