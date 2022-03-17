<?php
namespace M44\Terrains;
use M44\Board;

class SeaWall extends \M44\Models\Obstacle
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['sand']) && isset($hex['behavior']) && $hex['behavior'] == 'SEAWALL';
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Sea Wall');
    $this->number = 12;
    $this->defense = [\INFANTRY => -1, ARMOR => -1];
    $this->canIgnoreOneFlag = true;
    parent::__construct($row);
  }
}
