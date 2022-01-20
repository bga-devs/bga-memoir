<?php
namespace M44\Terrains;

class WinterRailroadBridge extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'wrailbridge';
    $this->name = clienttranslate('Winter Railroad Bridge');
    $this->landscape = 'winter';
    $this->water = true;
    $this->bridge = true;
    $this->rail = true;
  }
}
