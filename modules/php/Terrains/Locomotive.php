<?php
namespace M44\Terrains;

class Locomotive extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'loco';
    $this->name = clienttranslate('Locomotive');
    $this->transport = true;
    $this->rail = true;
  }
}
