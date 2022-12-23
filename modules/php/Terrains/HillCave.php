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
    $this->desc[] = clienttranslate('Japanese units must ignore all flags');
    $this->desc[] = clienttranslate('Attack on Japanese unit is done at -2 (except for Artillery)');
  }

  public function getPossibleAttackActions($unit)
  {
    // only an INFANTRY United States
    // with no adjacent ennemy
    // can try to seal the cave
    if (
      $unit->getFights() == 0 &&
      $unit->getType() == \INFANTRY &&
      $unit->getTeamId() == ALLIES &&
      !Board::isAdjacentToEnnemy($unit)
    ) {
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

  public function defense($unit)
  {
    $attackedUnit = Board::getUnitInCell($this->getPos());
    if ($attackedUnit->getType() == \INFANTRY && $attackedUnit->getNation() == 'jp') {
      if ($unit->getType() != \ARTILLERY) {
        return -2;
      } else {
        return 0;
      }
    }
    return parent::defense($unit);
  }

  public function mustIgnoreAllFlags($unit)
  {
    if ($unit->getNation() == 'jp') {
      return true;
    }
    return false;
  }
}
