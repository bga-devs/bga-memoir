<?php
namespace M44\Terrains;

class HillCave extends Hill
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['pcave']);
  }

  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Cave on a hill');
    $this->number = 52;
  }

  // TODO cave management
}
