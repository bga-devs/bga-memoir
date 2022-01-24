<?php
namespace M44\Terrains;

class Dam extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'dam';
    $this->name = clienttranslate('Dam');
    $this->landscape = 'country';
    $this->water = true;
  }
}
