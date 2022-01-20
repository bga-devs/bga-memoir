<?php
namespace M44\Terrains;

class Bunker extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'pbunker';
    $this->name = clienttranslate('Bunker');
    $this->landscape = 'jungle';
    $this->bunker = true;
  }
}
