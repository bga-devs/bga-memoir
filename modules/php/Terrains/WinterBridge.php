<?php
namespace M44\Terrains;

class WinterBridge extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'wbridge';
    $this->name = clienttranslate('Winter Bridge');
    $this->landscape = 'winter';
    $this->water = true;
    $this->bridge = true;
  }
}
