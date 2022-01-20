<?php
namespace M44\Terrains;

class DesertCurve extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'droadcurve';
    $this->name = clienttranslate('Desert Curve');
    $this->landscape = 'sand';
    $this->road = true;
  }
}
