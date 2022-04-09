<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Core\Stats;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Units;
use M44\Managers\Terrains;
use M44\Managers\Cards;
use M44\Helpers\Utils;
use M44\Board;
use M44\Dice;
use M44\Managers\Tokens;

trait AttackUnitsTrait
{
  /**
   * Automatically skip state if no more unit can attack
   */
  public function stAttackUnits()
  {
    $args = $this->argsAttackUnit();
    $nTargets = 0;
    foreach ($args['units'] as $uId => $targets) {
      $nTargets += count($targets);
      $unit = Units::get($uId);
      // if Combat engineer doesn't move and is on a mine field, it must sweep it
      if ($unit->mustSweep()) {
        foreach (Board::getTerrainsInCell($unit->getPos()) as $t) {
          if ($t instanceof \M44\Terrains\Minefield) {
            $t->onUnitEntering($unit, false);
            $nTargets -= count($targets);
          }
        }
      }

      // if unit moved and finished on a mine and there is a mine, it must explose
      if ($unit->getActivationOCard()->getType() == CARD_BEHIND_LINES && $unit->getMoves() < 3) {
        foreach (Board::getTerrainsInCell($unit->getPos()) as $t) {
          if ($t instanceof \M44\Terrains\Minefield) {
            $unit->setMoves(3);
            $t->onUnitEntering($unit, false);
          }
        }
      }
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
    if ($this->gamestate->state()['name'] == 'armorOverrun') {
      // in case of armor overrun , to close the previous attack
      $this->closeCurrentAttack(false);
    }

    // log attack information
    $stack = Globals::getAttackStack();
    $stack[] = [
      'pId' => $player->getId(),
      'unitId' => $unitId,
      'cardId' => $unit->getActivationCard(),
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
  public function getCurrentAttack($fetchAdditionalInfos = true)
  {
    $stack = Globals::getAttackStack();
    if (empty($stack)) {
      return null;
    }

    $currentAttack = $stack[count($stack) - 1];
    if ($fetchAdditionalInfos) {
      $currentAttack['unit'] = $currentAttack['unitId'] == -1 ? null : Units::get($currentAttack['unitId']);
      $currentAttack['oppUnit'] = Units::get($currentAttack['oppUnitId']);
      $currentAttack['player'] = Players::get($currentAttack['pId']);
      $currentAttack['card'] = Cards::get($currentAttack['cardId']);
    }
    return $currentAttack;
  }

  /**
   * Close current attack and jump depending on what's left in the stack
   */
  public function closeCurrentAttack($transition = true)
  {
    $stack = Globals::getAttackStack();
    $currentAttack = array_pop($stack);
    Globals::setAttackStack($stack);
    Globals::setUnitAttacker($currentAttack['unitId'] ?? null);

    if ($transition) {
      if (empty($stack)) {
        // No more pending attack, jump to next attack
        $this->changeActivePlayerAndJumpTo($currentAttack['pId'], \ST_ATTACK);
      } else {
        $newAttack = array_pop($stack);
        $this->nextState('nextAttack', $newAttack['pId']);

        // throw new \BgaVisibleSystemException('Resuming stacked attack is not implemented yet');
      }
    }
  }

  public function actNextAttack()
  {
    $this->closeCurrentAttack();
  }

  /**
   * Resolve the pending attack
   */
  public function stAttackThrow()
  {
    $attack = $this->getCurrentAttack();
    $unit = $attack['unit'];
    $oppUnit = $attack['oppUnit'];
    $player = $attack['player'];
    $card = $attack['card'];

    // Check if ambush was played and successfull
    if ($attack['ambush']) {
      if (is_null($unit)) {
        throw new \BgaVisibleSystemException('Ambush was played on a card attack');
      }
      // Check retreat
      if ($unit->getRetreats() > 0) {
        Notifications::message(
          clienttranslate('${player_name} unit has retreated due to Ambush. Attack cannot take place'),
          ['player' => $player]
        );
        $this->closeCurrentAttack();
        return;
      }

      // Check if attacking unit is still alive
      if ($unit->isEliminated()) {
        Notifications::message(clienttranslate('${player_name} unit has been destroyed. Attack cannot take place'), [
          'player' => $player,
        ]);
        $this->closeCurrentAttack();
        return;
      }
    }

    // Display who is attacking who
    Notifications::throwAttack($player, $unit, $attack['nDice'], $oppUnit);

    // Launch dice
    $results = Dice::roll($player, $attack['nDice'], $oppUnit->getPos());
    $coord = $oppUnit->getPos();

    // $hits = $oppUnit->getHits($results);
    $hits = $this->calculateHits($unit, $oppUnit, $card, $results);
    $eliminated = $this->damageUnit($oppUnit, $hits);
    if (Teams::checkVictory()) {
      return;
    }

    // Call listener for attacking unit (eg. to remove Wire for armors)
    if ($unit !== null) {
      foreach (Board::getTerrainsInCell($unit->getPos()) as $terrain) {
        $terrain->onAfterAttack($unit);
      }

      $unit->afterAttack($coord, $hits, $eliminated);
    }

    // Handle retreat
    if (isset($results[DICE_FLAG]) && !$eliminated) {
      $this->initRetreat($attack, $results);
      $this->nextState('retreat', $oppUnit->getPlayer());
    } elseif (
      Globals::isBritishCommand() &&
      $oppUnit->getNation() == 'brit' &&
      $attack['distance'] == 1 &&
      !$eliminated &&
      $oppUnit->getNUnits() == 1
    ) {
      $this->nextState('battleBack', $oppUnit->getPlayer());
    } else {
      $this->nextState('takeGround', $attack['pId']);
      // $this->closeCurrentAttack();
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
      if ($attacker !== null) {
        $hitAttacker = $attacker->getHitsOnTarget($type, $nb, $target);
        if ($hitAttacker != -1) {
          $hit = $hitAttacker;
        }
      }

      if ($card !== null) {
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
  public function damageUnit($unit, $hits, $cantRetreat = false, $ambush = false)
  {
    if ($hits == 0) {
      Notifications::miss($unit);
      return false;
    }

    // Take the hits
    $realHits = $unit->takeDamage($hits);
    // Increase the stats
    $attack = $this->getCurrentAttack();
    if ($attack !== null) {
      if (!$ambush) {
        $attacker = $attack['player'];
      } else {
        $attacker = $attack['oppUnit']->getPlayer();
      }
    } else {
      $attacker = $unit
        ->getTeam()
        ->getOpponent()
        ->getCommander();
    }

    $statName = 'inc' . $unit->getStatName() . 'FigRound' . Globals::getRound();
    Stats::$statName($attacker, $realHits);

    // Notify
    $player = $unit->getPlayer();
    Notifications::takeDamage($player, $unit, $hits, $cantRetreat);

    // Check for elimination
    if ($unit->isEliminated()) {
      Board::removeUnit($unit);
      $team = $attacker->getTeam();
      $team->addEliminationMedals($unit);
      Tokens::removeTargets($unit->getPos());

      // Increse the stat
      $statName = 'inc' . $unit->getStatName() . 'UnitRound' . Globals::getRound();
      Stats::$statName($attacker, 1);

      if (
        Globals::isItalyHighCommand() &&
        $unit->getTeamId() == \AXIS &&
        $unit
          ->getPlayer()
          ->getCards()
          ->count() > 3
      ) {
        $card = $unit
          ->getPlayer()
          ->getCards()
          ->rand();
        Cards::discard($card);
        Notifications::discardItalianHighCommand($unit->getPlayer(), $card);
      }
    }

    return $unit->isEliminated();
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

  public function actRemoveRoadBlock($unitId)
  {
    // Sanity checks
    self::checkAction('actRemoveRoadBlock');

    $player = Players::getCurrent();
    $args = $this->gamestate->state()['args'];
    if (!\array_key_exists($unitId, $args['units'])) {
      throw new \BgaVisibleSystemException('You cannot remove roadblock with this unit. Should not happen');
    }
    $cells = $args['units'][$unitId];
    $k = Utils::array_usearch($cells, function ($cell) {
      return ($cell['action'] ?? null) == 'actRemoveRoadBlock';
    });
    if ($k === false) {
      throw new \BgaVisibleSystemException('You cannot remove roadblock with this unit. Should not happen');
    }
    $info = $cells[$k];

    // Inc attack counter by 1
    $unit = Units::get($unitId);
    $unit->incFights(1);
    // Remove RoadBlock
    $terrain = Terrains::get($info['terrainId']);
    $terrain->removeFromBoard();
    Notifications::message(\clienttranslate('${player_name} removes RoadBlock on their hex'), ['player' => $player]);

    $this->nextState('attack');
  }

  public function actSealCave($unitId)
  {
    // Sanity checks
    self::checkAction('actSealCave');

    $player = Players::getCurrent();
    $args = $this->gamestate->state()['args'];
    if (!\array_key_exists($unitId, $args['units'])) {
      throw new \BgaVisibleSystemException('You cannot seal the cave with this unit. Should not happen');
    }
    $cells = $args['units'][$unitId];
    $k = Utils::array_usearch($cells, function ($cell) {
      return ($cell['action'] ?? null) == 'actSealCave';
    });
    if ($k === false) {
      throw new \BgaVisibleSystemException('You cannot seal the cave with this unit. Should not happen');
    }
    $info = $cells[$k];

    // Inc attack counter by 1
    $unit = Units::get($unitId);
    $unit->incFights(1);
    // throw dice
    $results = Dice::roll($player, $unit->getAttackPower()[0], $unit->getPos());

    // Compute number of heal
    $seal = $results[DICE_STAR] ?? 0 ? true : false;

    if ($seal) {
      $terrain = Terrains::get($info['terrainId']);
      $newTerrain = $terrain instanceof \M44\Terrains\HillCave ? 'hills' : 'mountain';
      $terrain->removeFromBoard();
      $terrain = Terrains::add([
        'type' => $newTerrain == 'hills' ? 'hill' : $newTerrain,
        'tile' => $newTerrain,
        'x' => $unit->getX(),
        'y' => $unit->getY(),
        'orientation' => 1,
      ]);

      Notifications::addTerrain(
        $player,
        $terrain,
        \clienttranslate('${player_name} seals the cave (in ${coordSource})')
      );
    } else {
      Notifications::message(clienttranslate('${player_name} fails to seal the cave'), ['player' => $player]);
    }

    $this->nextState('attack');
  }

  public function argsBattleBack()
  {
    $attack = $this->getCurrentAttack();
    return [
      'unitId' => $attack['oppUnit']->getId(),
      'target' => $attack['unit']->getId(),
      'cell' => $attack['unit']->getPos(),
    ];
  }

  public function actBattleBack()
  {
    self::checkAction('actBattleBack');
    $attack = $this->getCurrentAttack();

    Notifications::message(clienttranslate('${player_name} battles back with 1 die'), [
      'player' => $attack['oppUnit']->getPlayer(),
    ]);

    $oppUnit = $attack['unit'];
    $unit = $attack['oppUnit'];
    $oppPlayer = $oppUnit->getPlayer();
    $player = $unit->getPlayer();
    $results = Dice::roll($player, 1, $oppUnit->getPos());

    $hits = $this->calculateHits(null, $oppUnit, null, $results);
    $eliminated = $this->damageUnit($oppUnit, $hits);

    if (Teams::checkVictory()) {
      return;
    }

    $attack = [
      'pId' => $player->getId(),
      'unitId' => $unit->getId(),
      'x' => $unit->getX(),
      'y' => $unit->getY(),
      'oppUnitId' => $oppUnit->getId(),
      'nDice' => 1,
      'distance' => 1,
      'effect' => 'battleBack',
    ];

    if (isset($results[DICE_FLAG]) && !$eliminated) {
      $this->initRetreat($attack, $results);
      $this->nextState('retreat', $oppPlayer);
    } else {
      $this->closeCurrentAttack();
    }
  }

  public function actBattleBackPass()
  {
    self::checkAction('actBattleBackPass');

    $attack = $this->getCurrentAttack();

    Notifications::message(clienttranslate('${player_name} does not battle'), [
      'player' => $attack['oppUnit']->getPlayer(),
    ]);
    $this->closeCurrentAttack();
  }
}
