<?php
namespace M44\Terrains;

class Lighthouse extends Village
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['lighthouse']);
  }

  public function __construct($row)
  {
    parent::__construct($row);

    $this->name = clienttranslate('Lighthouses');
    $this->number = 27;
  }

  // TODO: scenario specific
}
