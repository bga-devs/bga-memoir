<?php
namespace M44\Terrains;

class Fortress extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'fortress';
    $this->name = clienttranslate('Fortress');
    $this->landscape = 'country';
    $this->landmark = true;
  }
}
