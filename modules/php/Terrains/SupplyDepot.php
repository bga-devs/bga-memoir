<?php
namespace M44\Terrains;

class SupplyDepot extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'depot';
    $this->name = clienttranslate('Supply Depot');
    $this->landscape = 'country';
    $this->landmark = true;
  }
}
