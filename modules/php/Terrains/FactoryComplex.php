<?php
namespace M44\Terrains;

class FactoryComplex extends Village
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['factory', 'wfactory']);
  }

  public function __construct($row)
  {
    parent::__construct($row);

    $this->name = clienttranslate('Factory Complex');
    $this->number = 21;
  }

  // TODO: scenario specific
}
