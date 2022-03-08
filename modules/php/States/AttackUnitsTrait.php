<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Units;
use M44\Managers\Terrains;
use M44\Helpers\Utils;
use M44\Board;

trait AttackUnitsTrait
{
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
      $this->actAttackUnitsDone(true);
    }
  }

  /**
   * Compute the units that can attack
   */
  public function argsAttackUnit($player = null)
  {
    $player = $player ?? Players::getActive();
    $card = $player->getCardInPlay();
    $args = $card->getArgsAttackUnits();
    Utils::clearPaths($args['units']);
    $args['lastUnitAttacker'] = Globals::getUnitAttacker();

    return $args;
  }

  /**
   * Attack phase is over, go to 'draw' phase
   */
  public function actAttackUnitsDone($auto = false)
  {
    if (!$auto) {
      self::checkAction('actAttackUnitsDone');
    }
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    $nextState = $card->nextStateAfterAttacks();
    $this->gamestate->nextState($nextState);
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
    $args = $this->gamestate->state()['args'];
    if (!\array_key_exists($unitId, $args['units'])) {
      throw new \BgaVisibleSystemException('You cannot attack with this unit. Should not happen');
    }
    $cells = $args['units'][$unitId];
    $k = Utils::searchCell($cells, $x, $y);
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
    $unit->incFights(1);

    // log attack information
    $stack = Globals::getAttackStack();
    $stack[] = [
      'pId' => $player->getId(),
      'unitId' => $unitId,
      'x' => $x,
      'y' => $y,
      'oppUnitId' => $oppUnit->getId(),
      'nDice' => $target['dice'],
      'distance' => $target['d'],
      'ambush' => false,
    ];
    Globals::setAttackStack($stack);

    $this->nextState('ambush', $oppUnit->getPlayer());
  }

  /**
   * Get current Attack
   */
  public function getCurrentAttack()
  {
    $stack = Globals::getAttackStack();
    $currentAttack = $stack[count($stack) - 1];
    $currentAttack['unit'] = $currentAttack['unitId'] == -1 ? null : Units::get($currentAttack['unitId']);
    $currentAttack['oppUnit'] = Units::get($currentAttack['oppUnitId']);
    $currentAttack['card'] = Players::get($currentAttack['pId'])->getCardInPlay();
    return $currentAttack;
  }

  /**
   * Close current attack and jump depending on what's left in the stack
   */
  public function closeCurrentAttack()
  {
    $stack = Globals::getAttackStack();
    $currentAttack = array_pop($stack);
    Globals::setAttackStack($stack);
    Globals::setUnitAttacker($currentAttack['unitId']);

    if (empty($stack)) {
      // No more pending attack, jump to next attack
      $this->changeActivePlayerAndJumpTo($currentAttack['pId'], \ST_ATTACK);
    } else {
      $newAttack = array_pop($stack);
      $this->nextState('nextAttack', $newAttack['pId']);

      // throw new \BgaVisibleSystemException('Resuming stacked attack is not implemented yet');
    }
  }

  /**
   * Resolve the pending attack
   */
  public function stAttackThrow()
  {
    $attack = $this->getCurrentAttack();
    $unit = $attack['unit']; // TODO : handle cards that attacks without activating units
    $oppUnit = $attack['oppUnit'];
    if ($unit != null) {
      $player = $unit->getPlayer();
    } else {
      $player = Players::getActive();
    }
    $card = $player->getCardInPlay();

    // Check if ambush was played and successfull
    if ($attack['ambush']) {
      // Check retreat
      if ($unit->getRetreats() > 0) {
        Notifications::message(
          clienttranslate('${player_name} unit has retreated due to Ambush. Attack cannot take place'),
          ['player' => $player]
        );
        $this->closeCurrentAttack();
        return;
      }
    }

    // Launch dice
    $results = array_count_values($this->rollDice($player, $attack['nDice'], $oppUnit->getPos()));

    // $hits = $oppUnit->getHits($results);
    $hits = $this->calculateHits($unit, $oppUnit, $card, $results);
    $eliminated = $this->damageUnit($oppUnit, $hits);

    // Handle retreat
    if (isset($results[DICE_FLAG]) && !$eliminated) {
      $this->initRetreat($attack, $results);
      $this->nextState('retreat', $oppUnit->getPlayer());
    } else {
      $this->closeCurrentAttack();
    }
  }

  /**
   * Calculate hits based on the units and cards
   *
   **/
  public function calculateHits($attacker, $target, $card, $results)
  {
    $hits = 0;

    foreach ($results as $type => $nb) {
      $hit = 0;

      // check hits of targeted unit
      $hit = $target->getHits($type, $nb);

      // check hits of attacker
      if ($attacker != null) {
        $hitAttacker = $attacker->getHitsOnTarget($type, $nb);
        if ($hitAttacker != -1) {
          $hit = $hitAttacker;
        }
      }

      if ($card != null) {
        // check hits of card
        $hitCards = $card->getHits($type, $nb);
        if ($hitCards != -1) {
          $hit = $hitCards;
        }
      }

      $hits += $hit;
    }
    return $hits;
  }

  /**
   * Damage a unit and return whether it's eliminated or not
   */
  public function damageUnit($unit, $hits, $cantRetreat = false)
  {
    if ($hits == 0) {
      return false;
    }

    $eliminated = $unit->takeDamage($hits);
    $player = $unit->getPlayer();
    Notifications::takeDamage($player, $unit, $hits, $cantRetreat);
    if ($eliminated) {
      Board::removeUnit($unit);
      $player = Players::get($this->getCurrentAttack()['pId']);
      $team = $player->getTeam();
      $team->addEliminationMedals($unit);
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
    $results = [DICE_GRENADE, \DICE_INFANTRY, DICE_STAR, DICE_ARMOR];

    Notifications::rollDice($player, $nDice, $results, $cell);
    return $results;
  }

  /**
   * Remove Wire instead of attacking
   */
  public function actRemoveWire($unitId)
  {
    // Sanity checks
    self::checkAction('actRemoveWire');

    $player = Players::getCurrent();
    $args = $this->gamestate->state()['args'];
    if (!\array_key_exists($unitId, $args['units'])) {
      throw new \BgaVisibleSystemException('You cannot remove wire with this unit. Should not happen');
    }
    $cells = $args['units'][$unitId];
    $k = Utils::array_usearch($cells, function ($cell) {
      return ($cell['action'] ?? null) == 'actRemoveWire';
    });
    if ($k === false) {
      throw new \BgaVisibleSystemException('You cannot remove wire with this unit. Should not happen');
    }
    $info = $cells[$k];

    // Inc attack counter by 1
    $unit = Units::get($unitId);
    $unit->incFights(1);
    // Remove wire
    $terrain = Terrains::get($info['terrainId']);
    $terrain->removeFromBoard();
    Notifications::message(\clienttranslate('${player_name} removes wire on their hex'), ['player' => $player]);

    $this->nextState('attack');
  }
}
