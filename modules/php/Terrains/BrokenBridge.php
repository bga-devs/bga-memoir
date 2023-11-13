<?php
namespace M44\Terrains;
use M44\Board;

class BrokenBridge extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['brkbridge']) &&
      (!isset($hex['behavior']) || !in_array($hex['behavior'], ['BRIDGE_SECTION']));
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Broken Bridge');
    $this->number = 9;
    $this->isBridge = false;
    $this->deltaAngle = -1;
    $this->canBeBlown = false;
    $this->oneMedalIfBlown = false;

    parent::__construct($row);
  }

  public function isImpassable($unit)
  {
    // Unit coming from a river cannot cross as it is on a boat (not for broken bridge)
    //if (Board::isRiverCell($unit->getPos())) {
    //  return true;
    //}

    return false;
  }
}
