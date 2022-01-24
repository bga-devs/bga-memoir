<?php
namespace M44\Terrains;

class Ropebridge extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'pbridge';
    $this->name = clienttranslate('Rope bridge');
    $this->landscape = 'jungle';
    $this->water = true;
    $this->bridge = true;
  }
}
