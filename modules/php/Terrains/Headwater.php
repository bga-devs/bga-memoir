<?php
namespace M44\Terrains;

class Headwater extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'pond';
    $this->name = clienttranslate('Headwater');
    $this->landscape = 'country';
    $this->water = true;
  }
}
