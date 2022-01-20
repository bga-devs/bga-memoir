<?php
namespace M44\Terrains;

class TrainStation extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'station';
    $this->name = clienttranslate('Train Station');
    $this->landscape = 'country';
    $this->rail = true;
  }
}
