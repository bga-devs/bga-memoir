<?php
namespace M44\Terrains;

class Pontoon extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'pontoon';
    $this->name = clienttranslate('Pontoon');
    $this->water = true;
    $this->bridge = true;
  }
}
