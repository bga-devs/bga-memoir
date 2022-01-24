<?php
namespace M44\Terrains;

class Hills extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'hills';
    $this->name = clienttranslate('Hills');
    $this->landscape = 'country';
    $this->elevation = true;
  }
}
