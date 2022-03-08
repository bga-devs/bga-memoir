<?php
namespace M44\Cards\Standard;

use M44\Managers\Units;
use M44\Board;
use M44\Core\Game;
use M44\Core\Globals;

class Barrage extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_BARRAGE;
    $this->name = clienttranslate('Barrage');
    $this->text = [
      clienttranslate('Target any 1 enemy unit.'),
      clienttranslate('Roll 4 battle dice, ignoring any terrain battle die reduction.'),
      clienttranslate("Score 1 hit for each die matching the unit's symbol or grenade."),
      clienttranslate('For each flag, retreat 1 hex.'),
      clienttranslate('Flags may not be ignored.'),
    ];
    $this->cannotIgnoreFlags = true;
  }

  public function nextStateAfterPlay()
  {
    return 'barrage';
  }

  public function argsTargetBarrage()
  {
    $player = $this->getPlayer();
    $otherTeam = $player->getTeam()->getId() == ALLIES ? AXIS : ALLIES;
    $oUnits = Units::getOfTeam($otherTeam);
    $units = [];

    foreach ($oUnits as $oUnit) {
      $unit = [];
      $unit['x'] = $oUnit->getX();
      $unit['y'] = $oUnit->getY();
      $unit['dice'] = 4;
      $units[$oUnit->getId()] = $unit;
    }

    return ['units' => $units];
  }

  public function actTargetBarrage($unitId)
  {
    $player = $this->getPlayer();
    // check that Ids are ennemy
    $args = $this->argsTargetBarrage();

    if (!in_array($unitId, array_keys($args['units']))) {
      throw new \feException('This unit cannot be attacked. Should not happen');
    }

    $stack = Globals::getAttackStack();
    $stack[] = [
      'pId' => $player->getId(),
      'unitId' => -1,
      'x' => $args['units'][$unitId]['x'],
      'y' => $args['units'][$unitId]['y'],
      'oppUnitId' => $unitId,
      'nDice' => $args['units'][$unitId]['dice'],
      'distance' => 0,
      'ambush' => false,
    ];
    Globals::setAttackStack($stack);
    Game::get()->nextState('attack');
  }
}
