<?php
namespace M44\Terrains;

class RailroadBridge extends Bridge
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['railbridge','wrailbridge']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Railroad Bridges');
    $this->number = 37;
    $this->deltaAngle = 2;
    $this->mustStopWhenEntering = [ARMOR, \ARTILLERY];
    $this->desc = [
      clienttranslate('No movement restriction for Infantry'),
      clienttranslate('Armor and Artillery moving in must stop'),
      clienttranslate('No combat restriction'),
      clienttranslate('Armor may Take Ground and Overrun'),
      clienttranslate('Do not block line of sight'),
    ];
    $this ->isRail = true;
    parent::__construct($row);
  }
}
