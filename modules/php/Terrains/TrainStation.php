<?php
namespace M44\Terrains;

class TrainStation extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'station';
    $this->name = clienttranslate('Train Station');
    $this->landscape = 'country';
    $this->rail = true;
  }
}
