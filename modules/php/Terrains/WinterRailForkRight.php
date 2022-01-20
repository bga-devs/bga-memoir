<?php
namespace M44\Terrains;

class WinterRailForkRight extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'wrailFR';
    $this->name = clienttranslate('Winter Rail Fork - Right');
    $this->landscape = 'winter';
    $this->rail = true;
  }
}
