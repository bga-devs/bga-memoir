<?php
namespace M44\Terrains;

class RadarStation extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['radar']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Radar stations');
    $this->number = 36;

    $this->desc = [
      clienttranslate('Unit moving in must stop and may move not further on that turn'),
      clienttranslate('Unit moving in may battle'),
      clienttranslate('Armor battles out at -2 dice'),
      clienttranslate('Unit may ignore one flag'),
      clienttranslate('Block line of sight'),
    ];

    $this->mustStopWhenEntering = true;
    $this->isBlockingLineOfSight = true;
    $this->defense = [INFANTRY => -1, ARMOR => -2];
    $this->offense = [ARMOR => -2];
    $this->canIgnoreOneFlag = true;
    parent::__construct($row);
  }
}
