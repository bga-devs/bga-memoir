<?php
namespace M44\Terrains;

class Barrack extends Village
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['barracks']);
  }

  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Barracks');
    $this->number = 17;
  }
}
