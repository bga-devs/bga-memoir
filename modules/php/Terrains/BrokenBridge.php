<?php
namespace M44\Terrains;

class BrokenBridge extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'brkbridge';
    $this->name = clienttranslate('Broken Bridge');
    $this->water = true;
    $this->bridge = true;
  }
}
