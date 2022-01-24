<?php
namespace M44\Terrains;

class Curve extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'roadcurve';
    $this->name = clienttranslate('Curve');
    $this->landscape = 'country';
    $this->road = true;
  }
}
