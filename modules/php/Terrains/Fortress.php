<?php
namespace M44\Terrains;
use M44\Board;

class Fortress extends Bunker
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['fortress']);
  }

  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Fortresses');
    $this->number = 24;
  }

  protected function isOriginalOwner($unit)
  {
    return true;
  }
}
