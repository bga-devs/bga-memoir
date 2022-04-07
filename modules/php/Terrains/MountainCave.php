<?php
namespace M44\Terrains;

class MountainCave extends Mountain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['pmcave']);
  }

  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Cave on mountains');
    $this->number = 53;
    $this->isCave = true;
  }

  // TODO cave management
}
