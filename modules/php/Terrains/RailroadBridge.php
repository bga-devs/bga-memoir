<?php
namespace M44\Terrains;

class RailroadBridge extends \M44\Models\RectTerrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['railbridge']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Railroad Bridges');
    $this->number = 37;
    $this->isBridge = true;
    $this->mustStopWhenEntering = [ARMOR, \ARTILLERY];
    $this->desc = [
      clienttranslate('No movement restriction for Infantry'),
      clienttranslate('Armor and Artillery moving in must stop'),
      clienttranslate('No combat restriction'),
      clienttranslate('Armor may Take Ground and Overrun'),
      clienttranslate('Do not block line of sight'),
    ];

    parent::__construct($row);
  }
}
