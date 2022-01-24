<?php
namespace M44\Terrains;

class Barracks extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'barracks';
    $this->name = clienttranslate('Barracks');
    $this->landscape = 'country';
    $this->landmark = true;
  }
}
