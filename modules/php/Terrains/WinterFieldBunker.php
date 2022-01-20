<?php
namespace M44\Terrains;

class WinterFieldBunker extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'wbunker';
    $this->name = clienttranslate('Winter Field Bunker');
    $this->landscape = 'winter';
    $this->bunker = true;
  }
}
