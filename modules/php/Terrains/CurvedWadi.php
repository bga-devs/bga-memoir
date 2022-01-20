<?php
namespace M44\Terrains;

class CurvedWadi extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'wcurve';
    $this->name = clienttranslate('Curved Wadi');
    $this->landscape = 'sand';
    $this->elevation = true;
  }
}
