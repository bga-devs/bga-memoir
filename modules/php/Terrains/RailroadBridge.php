<?php
namespace M44\Terrains;

class RailroadBridge extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'railbridge';
    $this->name = clienttranslate('Railroad Bridge');
    $this->water = true;
    $this->bridge = true;
    $this->rail = true;
  }
}
