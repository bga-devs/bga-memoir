<?php
namespace M44\Terrains;

class FrozenRiver extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'wcurved';
    $this->name = clienttranslate('Frozen River');
    $this->landscape = 'winter';
    $this->water = true;
  }
}
