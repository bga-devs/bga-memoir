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
   * Automatically skip state if no more unit can attack
   */
  public function stAttackUnits()
  {
    // throw new \feException(print_r(\debug_print_backtrace()));
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
   * Compute the units that can attack
   */
  public function argsAttackUnit($player = null)
  {
    $player = $player ?? Players::getActive();
    $ignoreFight = $this->gamestate->state()['name'] == 'armorOverrunAttack';

    $card = $player->getCardInPlay();
    $args = $card->getArgsAttackUnits($ignoreFight);
    Utils::clearPaths($args['units']);

    if ($ignoreFight) {
      $currentAttack = Globals::getCurrentAttack();
      return ['units' => [$currentAttack['unitId'] => $args['units'][$currentAttack['unitId']] ?? []]];
    }

    return $args;
  }

  /**
   * Attack phase is over, go to 'draw' phase
   */
  public function actAttackUnitsDone()
  {
    self::checkAction('actAttackUnitsDone');
    $this->gamestate->nextState('draw');
  }

  /**
   * Active player selected a unit and a cell he wants to attack with this unit
   */
  public function actAttackUnit($unitId, $x, $y)
  {
    // Sanity checks
    self::checkAction('actAttackUnit');

    $player = Players::getCurrent();
    $card = $player->getCardInPlay();
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
    if ($oppUnit === null) {
      throw new \BgaVisibleSystemException('No opponent unit in this cell. Should not happen');
    }

    // Prepare attack
    $unit = Units::get($unitId);
    if ($this->gamestate->state()['name'] == 'attackUnits') {
      $unit->incFights(1);
    }
    $nDice = $card->updateDiceRoll($target['dice']);

    // log attack information
    Globals::setCurrentAttack([
      'unitId' => $unitId,
      'x' => $x,
      'y' => $y,
      'oppUnitId' => $oppUnit->getId(),
      'nDice' => $nDice,
      'distance' => $target['d'],
      'ambush' => false,
    ]);

    $this->nextState('ambush', $oppUnit->getPlayer());
  }

  /**
   * Get current Attack
   */
  public function getCurrentAttack()
  {
    $currentAttack = Globals::getCurrentAttack();
    $currentAttack['unit'] = Units::get($currentAttack['unitId']);
    $currentAttack['oppUnit'] = Units::get($currentAttack['oppUnitId']);
    return $currentAttack;
  }

  /**
   * Resolve the pending attack
   */
  public function stAttackThrow()
  {
    $attack = $this->getCurrentAttack();
    $this->resolveAttack($attack);
  }

  /**
   * Resolve an attack
   */
  public function resolveAttack($attack)
  {
    $unit = $attack['unit'];
    $oppUnit = $attack['oppUnit'];
    $player = $unit->getPlayer();

    // Check if ambush was played and successfull
    if ($attack['ambush']) {
      // Check retreat
      if ($unit->getRetreat() > 0) {
        Notifications::message(
          clienttranslate('${player_name} unit has retreated due to Ambush. Attack cannot take place'),
          ['player' => $player]
        );
        $this->nextState('nextAttack');
        return;
      }
    }

    // Launch dice
    $results = array_count_values($this->rollDice($player, $attack['nDice'], $oppUnit->getPos()));

    // Handle hits : TODO handle cards and attacking unit
    $hits = $oppUnit->getHits($results);
    $eliminated = $this->damageUnit($oppUnit, $hits);

    if (isset($results[DICE_FLAG]) && !$eliminated) {
      $this->initRetreat($attack, $results);
    }

    // TODO: manage specific cards (behind ennemy lines...)

    $this->nextState('retreat', $oppUnit->getPlayer());
  }

  /**
   * Damage a unit and return whether it's eliminated or not
   */
  public function damageUnit($unit, $hits)
  {
    if ($hits == 0) {
      return false;
    }

    $eliminated = $unit->takeDamage($hits);
    $player = $unit->getPlayer();
    Notifications::takeDamage($player, $unit, $hits);
    if ($eliminated) {
      //TODO : Manage scenario specific
      // TODO : store type of unit
      Teams::incMedals(1, Players::get(Globals::getActivePlayer())->getTeam());
      Notifications::scoreMedal(Players::get(Globals::getActivePlayer()), 1);
    }

    return $eliminated;
  }

  /**
   * Roll dice : roll a given number of dices next to a given cell
   */
  public function rollDice($player, $nDice, $cell = null)
  {
    $dice = [\DICE_INFANTRY, \DICE_INFANTRY, \DICE_ARMOR, \DICE_FLAG, \DICE_STAR, \DICE_GRENADE];
    $results = [];
    for ($i = 0; $i < $nDice; $i++) {
      $k = array_rand($dice);
      $results[] = $dice[$k];
    }

    // debug
    $results = [DICE_INFANTRY, DICE_INFANTRY, DICE_FLAG, DICE_FLAG];

    Notifications::rollDice($player, $nDice, $results, $cell);
    return $results;
  }
}
