<?php
namespace M44\Terrains;

class Trench extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'ptrenches';
    $this->name = clienttranslate('Trench');
    $this->landscape = 'country';
    $this->manmade = true;
  }
}
