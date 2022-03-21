<?php
namespace M44\Terrains;

class Rail extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['rail', 'railcurve', 'railFL', 'railFR', 'railX']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Railroads');
    $this->number = 38;
    $this->desc = [
      clienttranslate('No movement restrictions for Infantry'),
      clienttranslate('Armor and Artillery moving onto must stop'),
      clienttranslate('Road crossing a railroad plays as standard road'),
      clienttranslate('No combat restrictions'),
      clienttranslate('Armor may Take ground and Overrun'),
      clienttranslate('Do not block line of sight'),
    ];
    $this->mustStopWhenEntering = [ARMOR, \ARTILLERY];
    parent::__construct($row);
  }
}
