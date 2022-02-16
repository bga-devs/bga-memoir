<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Units;
use M44\Helpers\Utils;
use M44\Board;

trait AttackUnitsTrait
{
  /**
   * Attack phase is over, go to 'draw' phase
   */
  public function actAttackUnitsDone()
  {
    self::checkAction('actAttackUnitsDone');
    $this->gamestate->nextState('draw');
  }

  /**
   * Compute the units that can attack
   */
  public function argsAttackUnit($player = null)
  {
    $player = $player ?? Players::getActive();
    $card = $player->getCardInPlay();
    return $card->getArgsAttackUnits();
  }

  /**
   * Automatically skip state if no more unit can attack
   */
  public function stAttackUnits()
  {
    $args = $this->argsAttackUnit();
    $nTargets = 0;
    foreach ($args['units'] as $targets) {
      $nTargets += count($targets);
    }
    if ($nTargets == 0) {
      $this->actAttackUnitsDone();
    }
  }

  /**
   * Active player selected a unit and a cell he wants to attack with this unit
   */
  public function actAttackUnit($unitId, $x, $y)
  {
    // Sanity checks
    self::checkAction('actAttackUnit');
    $player = Players::getCurrent();
    $args = $this->argsAttackUnit($player);
    if (!\array_key_exists($unitId, $args['units'])) {
      throw new \BgaVisibleSystemException('You cannot attack with this unit. Should not happen');
    }
    $cells = $args['units'][$unitId];
    $k = Utils::array_usearch($cells, function ($cell) use ($x, $y) {
      return $cell['x'] == $x && $cell['y'] == $y;
    });
    if ($k === false) {
      throw new \BgaVisibleSystemException('You cannot attack this hex with this unit. Should not happen');
    }
    $target = $cells[$k];
    $oppUnit = Board::getUnitInCell($x, $y);
    if($oppUnit === null){
      throw new \BgaVisibleSystemException('No opponent unit in this cell. Should not happen');
    }

    // Prepare attack
    $unit = Units::get($unitId);
    $nDice = $target['dice'];

    // if distance = 1, then ask for ambush
    if ($target['d'] == 1) {
      // TODO Globals::setPendingAttack(['unitId' => $unitId, 'x' => $x, 'y' => $y]);
      $this->gamestate->nextState('ambush');
    } else {
      $this->actResolveAttack($unit, $oppUnit, $nDice);
    }
  }


  /**
   * Resolve an attack
   */
  public function actResolveAttack($unit, $oppUnit, $nDice)
  {
    var_dump($unit->getId(), $oppUnit->getId(), $nDice);
    die("test");
  }


  public function rollDice($player, $nDice, $cell = null)
  {
    $dice = [
      \DICE_INFANTRY, \DICE_INFANTRY, \DICE_ARMOR, \DICE_FLAG, \DICE_STAR, \DICE_GRENADE
    ];
    $results = [];
    for($i = 0; $i < $nDice; $i++){
      $k = array_rand($dice);
      $results[] = $dice[$k];
    }

    Notifications::rollDice($player, $nDice, $results, $cell);
  }
}
