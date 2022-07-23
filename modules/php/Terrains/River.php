<?php
namespace M44\Terrains;
use M44\Board;
use M44\Core\Notifications;

class River extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['river', 'riverFL', 'riverFR', 'riverY', 'curve', 'pond', 'pmouth']) &&
      (!isset($hex['behavior']) || $hex['behavior'] == 'WIDE_RIVER');
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Rivers & Waterways');
    $this->number = 8;
    $this->desc = [\clienttranslate('Impassable, except over bridges')];
    $this->isImpassable = -1;
    $this->isRiver = true;

    parent::__construct($row);
  }

  public function isImpassable($unit)
  {
    if (Board::isBridgeCell(['x' => $this->x, 'y' => $this->y])) {
      return false;
    }
    // boat management
    if ($unit->getEquipment() == 'boat') {
      return false;
    }

    return true;
  }

  public function mustStopWhenEntering($unit)
  {
    if (Board::isBridgeCell(['x' => $this->x, 'y' => $this->y])) {
      return false;
    }
    if (Board::isRiverCell($unit->getPos())) {
      return false;
    }

    return true;
  }

  public function onUnitLeaving($unit, $isRetreat, $cell)
  {
    if (!Board::isRiverCell($cell) && $unit->getEquipment() == 'boat') {
      $unit->setExtraDatas('equipment', false);
      Notifications::removeStarToken($unit->getId(), $unit->getX(), $unit->getY());
    }
  }
}
