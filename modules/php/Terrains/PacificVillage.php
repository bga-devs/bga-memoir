<?php
namespace M44\Terrains;

class PacificVillage extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'pvillage';
    $this->name = clienttranslate('Pacific Village');
    $this->landscape = 'jungle';
    $this->buildings = true;
  }
}
