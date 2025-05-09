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
use M44\Helpers\Pieces;
use M44\Board;
use M44\Dice;
use M44\Managers\Tokens;

trait AttackUnitsTrait
{
  /**
   * Handle units that stopped on mines
   */
  public function stPreAttackUnits()
  {
    $args = $this->argsAttackUnit();
    $nTargets = 0;
    foreach ($args['units'] as $uId => $targets) {
      $unit = Units::get($uId);

      // if Combat engineer doesn't move and is on a mine field, it must sweep it
      if ($unit->mustSweep() && $unit->getMoves() == 0 && !$unit->isOnTheMove()) {
        foreach (Board::getTerrainsInCell($unit->getPos()) as $t) {
          if ($t instanceof \M44\Terrains\Minefield) {
            $t->onUnitEntering($unit, false, false);
            $nTargets -= count($targets);
          }
        }
      }

      // if unit moved and finished on a mine with Infiltration and there is a mine, it must explose
      $activationcard = $unit->getActivationOCard();
      if (($activationcard->getType() == CARD_BEHIND_LINES ||
        ($activationcard->getType() == CARD_COUNTER_ATTACK) &&
        $activationcard->getExtraDatas('copiedCardType') == CARD_BEHIND_LINES) && $unit->getMoves() < 3) {
        foreach (Board::getTerrainsInCell($unit->getPos()) as $t) {
          if ($t instanceof \M44\Terrains\Minefield) {
            $unit->setMoves(3);
            $t->onUnitEntering($unit, false, false);
          }
        }
      }
    }

    $this->nextState('');
  }

  /**
   * Automatically skip state if no more unit can attack
   */
  public function stAttackUnits()
  {
    $args = $this->argsAttackUnit();
    $nTargets = 0;
    foreach ($args['units'] as $uId => $targets) {
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

    if (Globals::isCampaign() && Globals::getAirPowerTokenUsed()) {
      $card = Cards::getInstance(CARD_AIR_POWER);
      $card->setPlayer($player->getId());
      $cardId = Cards::getIdByType(CARD_AIR_POWER);
      $card->setId($cardId); 
    } else {
      $card = $player->getCardInPlay();
    }

    $args = $card->getArgsAttackUnits();
    Utils::clearPaths($args['units']);
    $args['lastUnitAttacker'] = Globals::getUnitAttacker();
    $args['actionCount'] = Globals::getActionCount();

    return $args;
  }

  /**
   * Attack phase is over, go to 'draw' phase
   */
  public function actAttackUnitsDone($auto = false)
  {
    if (!$auto) {
      self::checkAction('actAttackUnitsDone');
      Globals::incActionCount();
    }
    $player = Players::getActive();
    
    if (Globals::isCampaign() && Globals::getAirPowerTokenUsed()) {
      $card = Cards::getInstance(CARD_AIR_POWER);
      $card->setPlayer($player->getId());
      $cardId = Cards::getIdByType(CARD_AIR_POWER);
      $card->setId($cardId); 
    } else {
      $card = $player->getCardInPlay();
    }

    $nextState = $card->nextStateAfterAttacks();
    $this->nextState($nextState);
  }

  /**
   * Active player selected a unit and a cell he wants to attack with this unit
   */
  public function actAttackUnit($unitId, $x, $y)
  {
    // Sanity checks
    self::checkAction('actAttackUnit');
    Globals::incActionCount();

    $player = Players::getCurrent();
    $card = $player->getCardInPlay();
    $args = $this->gamestate->state()['args'];
    $cells = $args['units'][$unitId] ?? null;
    if (is_null($cells)) {
      throw new \BgaVisibleSystemException('You cannot move this unit. Should not happen');
    }
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
      'bonusCloseAssault' => $unit->getBonusCloseAssault() == true && !$unit->isWounded() && $target['d'] == 1,
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
    $unit = $attack['unit'] ?? null;
    $oppUnit = $attack['oppUnit'] ?? null;
    $player = $attack['player'] ?? null ;
    $card = $attack['card'] ?? null;

    // if unit attack is camoufled, remove camouflage
    if ($unit != null) {
      if ($unit->isCamouflaged()) {
        Tokens::removeCamouflage($unit->getpos());
      }
    }


    // Check if ambush was played and successfull
    if (isset($attack['ambush']) && $attack['ambush']) {
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

      // recompute attack throw
      if ($attack['bonusCloseAssault'] && $unit->isWounded()) {
        Notifications::message(clienttranslate('Unit has been wounded and has lost its attack bonus'), []);
        $attack['nDice']--;
      }
    }

    // Display who is attacking who
    Notifications::throwAttack($player, $unit, $attack['nDice'] ?? 0, $oppUnit);

    // Launch dice
    $results = Dice::roll($player, $attack['nDice'], $oppUnit->getPos());
    $coord = $oppUnit->getPos();

    // $hits = $oppUnit->getHits($results);
    // if tiger is attacked double roll check for damages
    if ($oppUnit->getNumber() == '16') {
      $hits = $this->calculateHits($unit, $oppUnit, $card, $results);
      // Count all FLAGS and if Tiger would be blocked to retreat to add to hits for second roll 
      // (remove all related flags from 1st in this case results)
      if (isset($results[DICE_FLAG])) {
        $hitflag = $this->calculateTigerFlagsHits($attack, $results, $oppUnit);
        $hits += $hitflag;
        $results[DICE_FLAG] -= $hitflag;
      }

      // Second roll dice if hits >0 (armor and grenade)
      if ($hits > 0) {
        Notifications::message(clienttranslate('Tiger second roll'), []);
        $results2 = Dice::roll($player, $hits, $oppUnit->getPos());
        $hits2 = $this->calculateHitsTiger2ndRoll($results2);
        $eliminated = $this->damageUnit($oppUnit, $player, $hits2);
        if (Teams::checkVictory()) {
          return;
        }
      } else {
        $eliminated = false;
      }
    } else { // Standard case else than tigers
      $hits = $this->calculateHits($unit, $oppUnit, $card, $results);
      $eliminated = $this->damageUnit($oppUnit, $player, $hits);
      if (Teams::checkVictory()) {
        return;
      }
    }


    // Call listener for attacking unit (eg. to remove Wire for armors)
    if ($unit !== null) {
      foreach (Board::getTerrainsInCell($unit->getPos()) as $terrain) {
        $terrain->onAfterAttack($unit);
      }

      $unit->afterAttack($coord, $hits, $eliminated);
    }

    // Handle retreat
    if (
      isset($results[DICE_FLAG]) &&
      !$eliminated &&
      // unit is not on a boat on a river without a bridge
      ($oppUnit->getEquipment() != 'boat'
        || !Board::isRiverCell($oppUnit->getPos())
        || Board::isBridgeCell($oppUnit->getPos()))
    ) {
      $this->initRetreat($attack, $results);
      $this->nextState('retreat', $oppUnit->getPlayer());
    } elseif (
      Globals::isBritishCommand() &&
      $oppUnit->getNation() == 'brit' &&
      $attack['distance'] == 1 &&
      !$eliminated &&
      $oppUnit->getNUnits() == 1 &&
      !$oppUnit->cannotBattleBack() // ex Sniper do not benefit from british nation battle back
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
   * Calculate hits for tigers on the second dice roll (only Grenade hits)
   *
   **/
  public function calculateHitsTiger2ndRoll($results)
  {
    $hits = 0;

    foreach ($results as $type => $nb) {
      $hit = 0;

      // check hits of targeted Tiger for 2nd roll
      //$hit = $target->getHits($type, $nb); 
      // getHits Not used in this case as 1st roll is like std armor & 2nd is hit only on Grenade
      if ($type == \DICE_GRENADE) {
        $hits += 1;
      }
    }
    return $hits;
  }

  /**
   * Damage a unit and return whether it's eliminated or not
   */
  public function damageUnit($unit, $attacker, $hits, $cantRetreat = false, $ambush = false)
  {
    if ($hits == 0) {
      Notifications::miss($unit);
      return false;
    }

    // train case : if unit is Wagon, add also hits to loco and vice et versa if loco + wagon
    $train = Units::getAll()->filter(function ($unit) {
      return in_array($unit->getType(), [LOCOMOTIVE, WAGON]) && !$unit->isEliminated();
    });
    $trainIds = $train->getIds();
    $unitId = $unit->getId();
    $trainCase = count($trainIds) > 1 && in_array($unitId, $trainIds);
    if ($trainCase) {
      $firstUnitId[] = $unitId;
      $secondUnitToHitId = array_diff($trainIds, $firstUnitId);
    }


    // Take the hits
    $realHits = $unit->takeDamage($hits);
    if ($trainCase) {
      $secondUnitToHit = Units::get($secondUnitToHitId);
      $realHits2 = $secondUnitToHit->takeDamage($hits);
    }


    // Increase the stats, count only once for train case
    $statName = 'inc' . $unit->getStatName() . 'FigRound' . Globals::getRound();
    Stats::$statName($attacker, $realHits);
    if ($unit->isEliminated()) {
      // Increse the stat now to make sure it's sent to the front
      $statName = 'inc' . $unit->getStatName() . 'UnitRound' . Globals::getRound();
      Stats::$statName($attacker, 1);
    }

    // Notify damage
    $player = $unit->getPlayer();
    if ($unit->getType() == WAGON) {
      Notifications::takeDamage($player, $secondUnitToHit, $hits, $cantRetreat);
    } else {
      Notifications::takeDamage($player, $unit, $hits, $cantRetreat);
    }


    // Check for elimination
    // train case
    if ($trainCase && $secondUnitToHit->isEliminated()) {
      Board::removeUnit($secondUnitToHit);
      $team = $attacker->getTeam();
      $team->addEliminationMedals($secondUnitToHit);
      Tokens::removeTargets($secondUnitToHit->getPos());
      if ($secondUnitToHit->getType() == WAGON) {
        Notifications::removeUnitfromBoard($secondUnitToHit->getId());
      }
    }

    // standard unit case
    if ($unit->isEliminated()) {
      Board::removeUnit($unit);
      $team = $attacker->getTeam();
      $team->addEliminationMedals($unit);
      Tokens::removeTargets($unit->getPos());
      if ($unit->getType() == WAGON) {
        Notifications::removeUnitfromBoard($unit->getId());
      }

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
    Globals::incActionCount();

    $player = Players::getCurrent();
    $args = $this->gamestate->state()['args'];
    $cells = $args['units'][$unitId] ?? null;
    if (is_null($cells)) {
      throw new \BgaVisibleSystemException('You cannot move this unit. Should not happen');
    }
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
    Globals::incActionCount();

    $player = Players::getCurrent();
    $args = $this->gamestate->state()['args'];
    $cells = $args['units'][$unitId] ?? null;
    if (is_null($cells)) {
      throw new \BgaVisibleSystemException('You cannot move this unit. Should not happen');
    }
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
    Globals::incActionCount();

    $player = Players::getCurrent();
    $args = $this->gamestate->state()['args'];
    $cells = $args['units'][$unitId] ?? null;
    if (is_null($cells)) {
      throw new \BgaVisibleSystemException('You cannot move this unit. Should not happen');
    }
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
    $results = Dice::roll($player, $unit->getAttackPower($info)[0], $unit->getPos());

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
      'actionCount' => Globals::getActionCount(),
    ];
  }

  public function actBattleBack()
  {
    self::checkAction('actBattleBack');
    Globals::incActionCount();
    $attack = $this->getCurrentAttack();

    Notifications::message(clienttranslate('${player_name} battles back with 1 die'), [
      'player' => $attack['oppUnit']->getPlayer(),
    ]);

    $oppUnit = $attack['unit'];
    $unit = $attack['oppUnit'];
    $oppPlayer = $oppUnit->getPlayer();
    $player = $unit->getPlayer();
    $results = Dice::roll($player, 1, $oppUnit->getPos());

    // if tiger is battlebacked second roll check for damages
    if ($oppUnit->getNumber() == '16') {
      $hits = $this->calculateHits($unit, $oppUnit, null, $results);
      // Count all FLAGS and if Tiger would be blocked to retreat to add to hits for second roll 
      // (remove all related flags from 1st in this case results)
      if (isset($results[DICE_FLAG])) {
        $hitflag = $this->calculateTigerFlagsHits($attack, $results, $oppUnit);
        $hits += $hitflag;
        $results[DICE_FLAG] -= $hitflag;
      }
      // Second roll dice if hits >0 (armor and grenade)
      if ($hits > 0) {
        Notifications::message(clienttranslate('Tiger second roll'), []);
        $results2 = Dice::roll($player, $hits, $oppUnit->getPos());
        $hits2 = $this->calculateHitsTiger2ndRoll($results2);
        $eliminated = $this->damageUnit($oppUnit, $player, $hits2);
        if (Teams::checkVictory()) {
          return;
        }
      } else {
        $eliminated = false;
      }
    } else { // standard unit else than a tiger battlebacked

      $hits = $this->calculateHits($unit, $oppUnit, null, $results);
      $eliminated = $this->damageUnit($oppUnit, $player, $hits);


      if (Teams::checkVictory()) {
        return;
      }
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

    if (
      isset($results[DICE_FLAG]) &&
      !$eliminated &&
      // unit is not on a boat on a river
      ($oppUnit->getEquipment() != 'boat'
        || !Board::isRiverCell($oppUnit->getPos())
        || Board::isBridgeCell($oppUnit->getPos()))
    ) {
      $this->initRetreat($attack, $results);
      $this->nextState('retreat', $oppPlayer);
    } else {
      $this->closeCurrentAttack();
    }
  }

  public function actBattleBackPass()
  {
    self::checkAction('actBattleBackPass');
    Globals::incActionCount();

    $attack = $this->getCurrentAttack();

    Notifications::message(clienttranslate('${player_name} does not battle'), [
      'player' => $attack['oppUnit']->getPlayer(),
    ]);
    $this->closeCurrentAttack();
  }

  public function initTigerRetreat($attack, $dice, $targetUnit)
  {
    $canIgnore1Flag = Board::canIgnoreOneFlag($targetUnit);
    if (!$canIgnore1Flag) {
      $canIgnore1Flag = $targetUnit->canIgnoreOneFlag();
    }

    $canIgnoreAllFlags = Board::canIgnoreAllFlagsCell($targetUnit->getPos(), $targetUnit);
    $currentAttack = $this->getCurrentAttack();
    $mustIgnoreAllFlags = Board::mustIgnoreAllFlagsCell($targetUnit->getPos(), $targetUnit);

    if ($currentAttack['card']->cannotIgnoreFlags()) {
      $canIgnore1Flag = false;
      $canIgnoreAllFlags = false;
      $mustIgnoreAllFlags = false;
    } elseif ($targetUnit->getMustIgnore1Flag()) {
      $dice[\DICE_FLAG]--;
      if ($canIgnore1Flag && $dice[\DICE_FLAG] != 0) {
        $dice[\DICE_FLAG]--;
        $canIgnore1Flag = false;
      }
    }

    Globals::setRetreat([
      'min' => $mustIgnoreAllFlags ? 0 : ($canIgnoreAllFlags ? 0 : $dice[\DICE_FLAG] - ($canIgnore1Flag ? 1 : 0)),
      'max' => $mustIgnoreAllFlags ? 0 : $dice[\DICE_FLAG] * $targetUnit->getRetreatHex(),
      'unit' => $targetUnit->getId(),
      'effect' => $attack['effect'] ?? '',
    ]);
  }


  public function argsRetreatTigerUnit($unit, $clearPaths = false)
  {
    list($unit, $minFlags, $maxFlags, $effect) = $this->getTigerRetreatInfo();
    $attack = $this->getCurrentAttack();
    $args = array_merge(Board::getArgsRetreat($unit, $minFlags, $maxFlags), [
      'unitId' => $unit->getId(),
      'min' => $minFlags,
      'max' => $maxFlags,
      'desc' =>
      $minFlags == $maxFlags
        ? ''
        : [
          'log' => \clienttranslate('(up to ${max} cells)'),
          'args' => ['max' => $maxFlags],
        ],
      'i18n' => ['desc'],
      'titleSuffix' => $effect . ($minFlags == 0 ? 'skippable' : ''),
      'actionCount' => Globals::getActionCount(),
      'attackingUnit' => $attack['unitId'],
      'attackUnits' => $this->argsAttackUnit($attack['player'])['units'],
    ]);
    Utils::clearPaths($args['units'], $clearPaths); // Remove paths, useless for UI
    return $args;
  }

  public function getTigerRetreatInfo()
  {
    $data = Globals::getRetreat();

    return [Units::get($data['unit']), $data['min'], $data['max'], $data['effect']];
  }

  public function calculateTigerFlagsHits($attack, $results1, $targetunit)
  {
    $this->initTigerRetreat($attack, $results1, $targetunit);
    $args = $this->argsRetreatTigerUnit($targetunit);
    return $args['hits'];
  }
}
