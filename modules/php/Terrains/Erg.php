<?php
namespace M44\Terrains;
use M44\Board;

class Erg extends Hill
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['dridge']);
  }

  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Ergs & Ridges');
    $this->number = 64;
    $this->mustStopWhenEntering = true;
  }
}
