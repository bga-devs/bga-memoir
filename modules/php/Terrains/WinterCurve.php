<?php
namespace M44\Terrains;

class WinterCurve extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'wroadcurve';
    $this->name = clienttranslate('Winter Curve');
    $this->landscape = 'winter';
    $this->road = true;
  }
}
