<?php
namespace M44\Terrains;

class WinterForest extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'wforest';
    $this->name = clienttranslate('Winter Forest');
    $this->landscape = 'winter';
    $this->vegetation = true;
  }
}
