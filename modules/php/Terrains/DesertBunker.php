<?php
namespace M44\Terrains;

class DesertBunker extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'dbunker';
    $this->name = clienttranslate('Desert Bunker');
    $this->landscape = 'sand';
    $this->bunker = true;
  }
}
