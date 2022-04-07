<?php
namespace M44\Terrains;

use M44\Board;

class MountainCave extends Mountain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['pmcave']);
  }

  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Cave on mountains');
    $this->number = 53;
    $this->isCave = true;
  }

  public function getPossibleAttackActions($unit)
  {
    // only an INFANTRY United States
    // with no adjacent ennemy
    // can try to seal the cave
    if ($unit->getType() == \INFANTRY && $unit->getTeamId() == ALLIES && !Board::isAdjacentToEnnemy($unit)) {
      return [
        [
          'desc' => \clienttranslate('Attempt to seal the cave'),
          'action' => 'actSealCave',
        ],
      ];
    } else {
      return [];
    }
  }
}
