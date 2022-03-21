<?php
namespace M44\Terrains;

class PalmForest extends Forest
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['palmtrees']);
  }

  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Palm Forests');
    $this->number = 32;
  }
}
