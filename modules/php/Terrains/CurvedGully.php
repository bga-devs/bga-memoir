<?php
namespace M44\Terrains;

class CurvedGully extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'cravine';
    $this->name = clienttranslate('Curved Gully');
    $this->landscape = 'country';
    $this->elevation = true;
  }
}
