<?php
namespace M44\Terrains;

class HillVillage extends Village
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['whillvillage']);
  }

  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Hills with Villages');
    $this->number = 49;
  }
}
