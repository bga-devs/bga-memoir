<?php
namespace M44\Terrains;

class RoadBlock extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['roadblock']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Road Blocks');
    $this->number = 40;
    $this->desc = [
      clienttranslate('Infantry moving in must stop and may move no further on that turn'),
      clienttranslate('Impassable by Armor & Infantry'),
      clienttranslate('Unit may ignore one flag'),
      clienttranslate('Do not block line of sight'),
    ];
    $this->mustStopWhenEntering = true;
    $this->isImpassable = [ARMOR, \ARTILLERY];
    $this->canIgnoreOneFlag = true;
    parent::__construct($row);
  }
}
