<?php
namespace M44\Terrains;

class Beach extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return false;
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Beaches');
    $this->number = 1;
    $this->isBeach = true;
    // Maximum movement onto beaches is 2 hexes

    parent::__construct($row);
  }
}
