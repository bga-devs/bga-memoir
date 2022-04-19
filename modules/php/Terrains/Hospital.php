<?php
namespace M44\Terrains;
use M44\Board;

class Hospital extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['phospital']);
  }

  public function __construct($row)
  {
    parent::__construct($row);

    $this->name = clienttranslate('Hospitals');
    $this->number = 55;
    $this->isBlockingLineOfSight = true;
  }

  public function getPossibleMoveActions($unit)
  {
    // only an INFANTRY
    // wounded
    // that did not move
    // with no adjacent ennemy
    // can be healed
    if (
      $unit->getType() == \INFANTRY &&
      $unit->isWounded() &&
      $unit->getMoves() == 0 &&
      !Board::isAdjacentToEnnemy($unit)
    ) {
      return [
        [
          'desc' => \clienttranslate('Heal the unit'),
          'action' => 'actHealUnitHospital',
        ],
      ];
    } else {
      return [];
    }
  }
}
