<?php
namespace M44\Terrains;

class Rail extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['rail', 'railcurve', 'railFL', 'railFR', 'railX', 'wrail', 'wrailcurve']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Railroads');
    $this->number = 38;
    $this->mustStopMovingWhenEntering = [ARMOR, ARTILLERY];
    $this->desc = [
      clienttranslate('Armor may Take ground and Overrun'),
      clienttranslate('Road crossing a railroad plays as standard road'),
    ];
    $this ->isRail = true;
    parent::__construct($row);
  }

  // TODO Rail
}
