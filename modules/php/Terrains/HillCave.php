<?php
namespace M44\Terrains;

use M44\Board;

class HillCave extends Hill
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['pcave']);
  }

  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Cave on a hill');
    $this->number = 52;
    $this->isCave = true;
    $this->isImpassable = [ARMOR, \ARTILLERY];
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
