<?php
namespace M44\Terrains;

class WinterVillage extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'wvillage';
    $this->name = clienttranslate('Winter Village');
    $this->landscape = 'winter';
    $this->buildings = true;
  }
}
