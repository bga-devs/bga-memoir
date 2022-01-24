<?php
namespace M44\Terrains;

class Gully extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'ravine';
    $this->name = clienttranslate('Gully');
    $this->landscape = 'country';
    $this->elevation = true;
  }
}
