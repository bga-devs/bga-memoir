<?php
namespace M44\Terrains;

class Bridge extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'bridge';
    $this->name = clienttranslate('Bridge');
    $this->water = true;
    $this->bridge = true;
  }
}
