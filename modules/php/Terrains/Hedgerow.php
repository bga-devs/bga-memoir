<?php
namespace M44\Terrains;

class Hedgerow extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['hedgerow']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Hedgerows');
    $this->number = 4;

    $this->mustBeAdjacentToEnter = true;
    $this->mustStopWhenEntering = true;
    $this->enteringCannotBattle = true;
    $this->isBlockingLineOfSight = true;
    $this->defense = [INFANTRY => -1, ARMOR => -2];
    parent::__construct($row);
  }
}
