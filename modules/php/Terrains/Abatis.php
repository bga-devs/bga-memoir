<?php
namespace M44\Terrains;

class Abatis extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'abatis';
    $this->name = clienttranslate('Abatis');
    $this->landscape = 'country';
    $this->block = true;
  }
}
