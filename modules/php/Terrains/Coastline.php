<?php
namespace M44\Terrains;

class Coastline extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'coastcurve';
    $this->name = clienttranslate('Coastline');
    $this->landscape = 'sand';
    $this->water = true;
  }
}
