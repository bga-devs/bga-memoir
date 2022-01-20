<?php
namespace M44\Terrains;

class WinterCastle extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'wcastle';
    $this->name = clienttranslate('Winter Castle');
    $this->landscape = 'winter';
    $this->landmark = true;
  }
}
