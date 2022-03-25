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
    $this->canIgnoreAllFlags = true;
    $this->desc[] = clienttranslate('Unit may ignore all flags');
  }

  public function isOriginalOwner($unit)
  {
    return false;
  }
}
