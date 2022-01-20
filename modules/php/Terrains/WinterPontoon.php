<?php
namespace M44\Terrains;

class WinterPontoon extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'wpontoon';
    $this->name = clienttranslate('Winter Pontoon');
    $this->landscape = 'winter';
    $this->water = true;
    $this->bridge = true;
  }
}
