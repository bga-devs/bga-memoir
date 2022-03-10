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
    $units = $this->getPlayer()
      ->getTeam()
      ->getOpponent()
      ->getUnits();

    return ['unitIds' => $units->getIds()];
  }

  public function actTargetBarrage($unitId)
  {
    $args = $this->argsTargetBarrage();
    if (!in_array($unitId, $args['unitIds'])) {
      throw new \feException('This unit cannot be attacked. Should not happen');
    }
    $unit = Units::get($unitId);
    $stack = Globals::getAttackStack();

    $stack[] = [
      'pId' => $this->pId,
      'unitId' => -1,
      'x' => $unit->getX(),
      'y' => $unit->getY(),
      'oppUnitId' => $unitId,
      'nDice' => 4,
      'distance' => 0,
      'ambush' => false,
    ];
    Globals::setAttackStack($stack);
    Game::get()->nextState('attack');
  }
}
