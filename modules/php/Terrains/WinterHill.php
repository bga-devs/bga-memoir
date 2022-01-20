<?php
namespace M44\Terrains;

class WinterHill extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'whill';
    $this->name = clienttranslate('Winter Hill');
    $this->landscape = 'winter';
    $this->elevation = true;
  }
}
