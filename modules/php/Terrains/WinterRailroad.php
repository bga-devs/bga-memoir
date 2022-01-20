<?php
namespace M44\Terrains;

class WinterRailroad extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'wrail';
    $this->name = clienttranslate('Winter Railroad');
    $this->landscape = 'winter';
    $this->rail = true;
  }
}
