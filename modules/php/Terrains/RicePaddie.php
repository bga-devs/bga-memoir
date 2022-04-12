<?php
namespace M44\Terrains;
use M44\Board;

class RicePaddie extends Marsh
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['price']);
  }

  public function __construct($row)
  {
    parent::__construct($row);

    $this->name = clienttranslate('Rice Paddies');
    $this->number = 60;
  }
}
