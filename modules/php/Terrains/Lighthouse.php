<?php
namespace M44\Terrains;

class Lighthouse extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'lighthouse';
    $this->name = clienttranslate('Lighthouse');
    $this->landscape = 'country';
    $this->landmark = true;
  }
}
