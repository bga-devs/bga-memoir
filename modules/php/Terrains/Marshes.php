<?php
namespace M44\Terrains;

class Marshes extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'marshes';
    $this->name = clienttranslate('Marshes');
    $this->landscape = 'country';
    $this->vegetation = true;
  }
}
