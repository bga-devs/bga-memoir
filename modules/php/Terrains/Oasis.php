<?php
namespace M44\Terrains;
use M44\Board;

class Oasis extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['oasis']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Oasis');
    $this->number = 31;

    $this->mustStopWhenEntering = true;
    $this->isBlockingLineOfSight = true;
    $this->canIgnoreOneFlag = true;
    $this->defense = [INFANTRY => -1, ARMOR => -1];
    // $this->desc = [
    //   // \clienttranslate('Unit moving in must stop and may move no further on that turn'),
    //   clienttranslate('Unit moving in may still battle'),
    //   clienttranslate('Unit may ignore one flag'),
    //   clienttranslate('Blocks line of sight'),
    // ];
    parent::__construct($row);
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
          'action' => 'actHealUnit',
        ],
      ];
    } else {
      return [];
    }
  }
}
