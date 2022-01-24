<?php
namespace M44\Terrains;

class Airfield extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'airfield';
    $this->name = clienttranslate('Airfield');
    $this->landscape = 'country';
    $this->air = true;
  }
}
