<?php
namespace M44\Terrains;

class Railroad extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'rail';
    $this->name = clienttranslate('Railroad');
    $this->landscape = 'country';
    $this->rail = true;
  }
}
