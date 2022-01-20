<?php
namespace M44\Terrains;

class HQSupplyTents extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'dcamp';
    $this->name = clienttranslate('HQ-Supply Tents');
    $this->landscape = 'sand';
    $this->landmark = true;
  }
}
