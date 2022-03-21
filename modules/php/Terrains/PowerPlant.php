<?php
namespace M44\Terrains;

class PowerPlant extends Village
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['powerplant']);
  }

  public function __construct($row)
  {
    parent::__construct($row);

    $this->name = clienttranslate('Power Plants');
    $this->number = 34;
  }

  // TODO: scenario specific
}
