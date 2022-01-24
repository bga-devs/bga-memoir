<?php
namespace M44\Terrains;

class Cave extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'pcave';
    $this->name = clienttranslate('Cave');
    $this->landscape = 'country';
    $this->elevation = true;
    $this->manmade = true;
  }
}
