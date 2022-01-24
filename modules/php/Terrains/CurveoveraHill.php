<?php
namespace M44\Terrains;

class CurveoveraHill extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'hillcurve';
    $this->name = clienttranslate('Curve over a Hill');
    $this->landscape = 'country';
    $this->elevation = true;
    $this->road = true;
  }
}
