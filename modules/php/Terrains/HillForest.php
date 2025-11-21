<?php
namespace M44\Terrains;

class HillForest extends Forest
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['whillforest']);
  }

  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Hills with Forests');
    $this->number = 48;
    $this->height = 1;
  }
}
