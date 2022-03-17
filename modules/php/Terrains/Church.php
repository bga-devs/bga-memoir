<?php
namespace M44\Terrains;

class Church extends Village
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['church']);
  }

  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Churches');
    $this->number = 19;
    $this->canIgnoreOneFlag = true;
  }
}
