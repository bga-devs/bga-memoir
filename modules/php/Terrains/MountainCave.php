<?php
namespace M44\Terrains;

class MountainCave extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'pmcave';
    $this->name = clienttranslate('Mountain Cave');
    $this->landscape = 'country';
    $this->elevation = true;
    $this->manmade = true;
  }
}
