<?php
namespace M44\Terrains;

class Destroyership extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'pdestroyer';
    $this->name = clienttranslate('Destroyer ship');
    $this->transport = true;
    $this->water = true;
  }
}
