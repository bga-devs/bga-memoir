<?php
namespace M44\Terrains;

class CurvedGully extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'cravine';
    $this->name = clienttranslate('Curved Gully');
    $this->landscape = 'country';
    $this->elevation = true;
  }
}
