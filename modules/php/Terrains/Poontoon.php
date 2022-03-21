<?php
namespace M44\Terrains;

class Poontoon extends \M44\Models\RectTerrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['pontoon']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Poonton Bridges');
    $this->number = 33;
    $this->isBridge = true;
    // TODO ?
    parent::__construct($row);
  }
}
