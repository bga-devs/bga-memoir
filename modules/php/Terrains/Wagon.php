<?php
namespace M44\Terrains;

class Wagon extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'wagon';
    $this->name = clienttranslate('Wagon');
    $this->transport = true;
    $this->rail = true;
  }
}
