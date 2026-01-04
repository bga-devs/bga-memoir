<?php

namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Units;
use M44\Helpers\Utils;
use M44\Helpers\Log;
use M44\Board;

trait RetreatUnitTrait
{
  /**
   * Initialize a retreat
   */
  public function initRetreat($attack, $dice)
  {
    $oppUnit = Units::get($attack['oppUnitId']);
    $canIgnore1Flag = Board::canIgnoreOneFlag($oppUnit);
    if (!$canIgnore1Flag) {
      $canIgnore1Flag = $oppUnit->canIgnoreOneFlag();
    }

    $canIgnoreAllFlags = Board::canIgnoreAllFlagsCell($oppUnit->getPos(), $oppUnit);
    $currentAttack = $this->getCurrentAttack();
    $mustIgnoreAllFlags = Board::mustIgnoreAllFlagsCell($oppUnit->getPos(), $oppUnit);

    if ($currentAttack['card']->cannotIgnoreFlags()) {
      $canIgnore1Flag = false;
      $canIgnoreAllFlags = false;
      $mustIgnoreAllFlags = false;
    } elseif ($oppUnit->getMustIgnore1Flag()) {
      $dice[\DICE_FLAG]--;
      if ($canIgnore1Flag && $dice[\DICE_FLAG] != 0) {
        $dice[\DICE_FLAG]--;
        $canIgnore1Flag = false;
      }
    }

    $attackedUnit = Units::get($attack['oppUnitId']);
    // TODO : compute the min/max flags

    Globals::setRetreat([
      'min' => $mustIgnoreAllFlags ? 0 : ($canIgnoreAllFlags ? 0 : $dice[\DICE_FLAG] - ($canIgnore1Flag ? 1 : 0)),
      'max' => $mustIgnoreAllFlags ? 0 : $dice[\DICE_FLAG] * $attackedUnit->getRetreatHex(),
      'unit' => $attack['oppUnitId'],
      'effect' => $attack['effect'] ?? '',
    ]);
  }

  /**
   * Fetch retreat relevant informations
   */
  public function getRetreatInfo()
  {
    $data = Globals::getRetreat();
    // $attack = $this->getCurrentAttack();
    return [Units::get($data['unit']), $data['min'], $data['max'], $data['effect']];
  }

  /**
   * Automatically resolve state if only one possible choice
   */
  public function stRetreatUnit()
  {
    // Take the hits if any
    $args = $this->argsRetreatUnit();
    $unit = Units::get($args['unitId']);
    $minDistCells = $args['cells'];
    Utils::filter($minDistCells, function ($cell) use ($args) {
      return $cell['d'] == $args['min'];
    });

    if ($args['hits'] > 0 && !$unit->isEliminated()) {
      $attacker = $unit
        ->getTeam()
        ->getOpponent()
        ->getCommander();
      $eliminated = $this->damageUnit($unit, $attacker, $args['hits'], true);
      $retreatInfo = Globals::getRetreat();
      $retreatInfo['min'] -= $args['hits'];
      if ($unit->isEliminated()) {
        $retreatInfo['min'] = 0;
      }
      Globals::setRetreat($retreatInfo);
      if (Teams::checkVictory()) {
        // in case of no remaining units of the opponent player (or this team's unit removed) 
        // due to too many unsuccesfull airdrops
        $player = $unit->getTeam()->getCommander();
        $this->nextState('endRound', $player);
        return;
      }

      if ($this->getCurrentAttack()['unit'] != null) {
        $this->getCurrentAttack()['unit']->afterAttackRetreatHit($unit->getPos(), $args['hits'], $eliminated);
      }

      if ($unit->isEliminated()) {
        $this->actRetreatUnitDone(true);
      } else {
        $this->nextState('retreat');
      }
    }
    // If no more cells, auto-done
    elseif (empty($args['cells']) || $unit->isEliminated()) {
      $this->actRetreatUnitDone(true);
    }
    // If only one cell, retreat to that
    elseif (count($args['cells']) == 1 && $args['min'] > 0) {
      $cell = reset($args['cells']);
      $this->actRetreatUnit($cell['x'], $cell['y'], true);
    }
    // If only one cell at good distance, retreat to that
    elseif (count($minDistCells) == 1) {
      $cell = reset($minDistCells);
      $this->actRetreatUnit($cell['x'], $cell['y'], true);
    } else {
      $this->giveExtraTime($unit->getPlayer()->getId(), 20);
    }
  }

  /**
   * Compute the units that can attack
   */
  public function argsRetreatUnit($clearPaths = false)
  {
    $player = Players::getActive();
    list($unit, $minFlags, $maxFlags, $effect) = $this->getRetreatInfo();
    $attack = $this->getCurrentAttack();
    // Case train detected if train case
    $train = Units::getAll()->filter(function ($unit) {
      return in_array($unit->getType(), [LOCOMOTIVE, WAGON]) && !$unit->isEliminated();
    });

    $trainCase = in_array($unit->getType(), [LOCOMOTIVE, WAGON]);

    // check wether it is locomotive only or locomotive + wagon train
    if ($trainCase) {
      if (count($train) > 1 && $unit->getType() == LOCOMOTIVE) {
        $wagon = $train->filter(function ($unit) {
          return in_array($unit->getType(), [WAGON]);
        });
        $trainUnitToRetreat = $wagon->first();
      } else {
        $trainUnitToRetreat = $unit;
      }
      $unit = $trainUnitToRetreat;
    }


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

  public function actIgnore1Flag()
  {
    self::checkAction('actIgnore1Flag');
    Globals::incActionCount();
    $args = $this->argsRetreatUnit();
    $player = Players::getCurrent();
    if ($args['min'] == $args['max']) {
      throw new \BgaVisibleSystemException('You cannot ignore 1 flag. Should not happen');
    }

    $r = Globals::getRetreat();
    $r['max'] -= 1;
    Globals::setRetreat($r);

    $this->nextState('retreat');
  }

  public function actRetreatUnit($x, $y, $auto = false)
  {
    // Sanity checks
    if (!$auto) {
      self::checkAction('actRetreatUnit');
      Globals::incActionCount();
    }
    $args = $this->argsRetreatUnit();
    $player = Players::getCurrent();
    $cells = $args['cells'];
    $k = Utils::searchCell($cells, $x, $y);
    if ($k === false) {
      throw new \BgaVisibleSystemException('You cannot move this unit to this hex. Should not happen');
    }

    // Train case
    // move all train units at once forward from locomotive or backward from loco or wagon if it exists
    $train = Units::getAll()->filter(function ($unit) {
      return in_array($unit->getType(), [LOCOMOTIVE, WAGON]) && !$unit->isEliminated();
    });
    $trainIds = $train->getIds();
    $unitId = $args['unitId'];
    $trainCase = count($trainIds) > 1 && in_array($unitId, $trainIds);
    if ($trainCase) {
      $firstUnitId[] = $unitId;
      $secondUnitToMoveId = array_diff($trainIds, $firstUnitId);
    }

    // Move the unit
    $cell = $cells[$k];
    $dist = $cell['d'];
    usort($cell['paths'], function ($path1, $path2) {
      return $path1['resistance'] - $path2['resistance'];
    });
    $path = $cell['paths'][0]; // Take the least resistance path
    $unitId = $args['unitId'];

    $unit = Units::get($unitId);
    $coordSource = $unit->getPos();
    foreach ($path['cells'] as $c) {
      Notifications::retreatUnit($unit->getPlayer(), $unit, $coordSource, $c);
      list($interrupted, $isWinning) = Board::moveUnit($unit, $c, true);
      if ($trainCase) {
        $c2 = $c;
        $c2['x'] = $coordSource['x'];
        $c2['y'] = $coordSource['y'];
        $secondUnitToMove = Units::get($secondUnitToMoveId);
        $secondUnitPos = $secondUnitToMove->getPos();
        Notifications::moveUnitNoMsg($player, $secondUnitToMove, $secondUnitPos, $c2);
        $tmp = Board::moveUnit($secondUnitToMove, $c2);
      }
      if ($isWinning) {
        return;
      } elseif ($interrupted) {
        $this->nextState('retreat');
        return;
      }

      $coordSource = $c;
    }

    // Update min/max depending on the number of retreat done already
    $unit->incRetreats($dist);
    $r = Globals::getRetreat();
    $r['min'] -= $dist;
    if ($r['min'] < 0) {
      $r['min'] = 0;
    }
    $r['max'] -= $dist;
    Globals::setRetreat($r);

    $this->nextState('retreat');
  }

  public function actRetreatUnitDone($auto = false)
  {
    // Sanity checks
    if (!$auto) {
      self::checkAction('actRetreatUnitDone');
      Globals::incActionCount();
    }
    // check that retreat = 0
    list($unit, $minFlags,, $effect) = $this->getRetreatInfo();
    if (!$unit->isEliminated() && $minFlags > 0) {
      throw new \BgaUserException(clienttranslate('You did not retreat far enough. Should not happen.'));
    }

    Log::checkpoint(); // Make undo invalid

    $attack = $this->getCurrentAttack();
    $oppUnit = $attack['oppUnit'];
    if ($attack['unit'] == null || $effect == 'battleBack') {
      // Attack triggered by a card without order
      $this->closeCurrentAttack();
    } elseif (
      Globals::isBritishCommand() &&
      $oppUnit->getNation() == 'brit' &&
      $attack['distance'] == 1 &&
      !$attack['ambush'] &&
      $oppUnit->getNUnits() == 1 &&
      $oppUnit->getRetreats() == 0 &&
      !$oppUnit->cannotBattleBack() && // ex Sniper do not benefit from british nation battle back
      !$oppUnit->cannotBattleTerrainRestriction() // ex unit in ocean cannot battleback
    ) {
      $oppUnit->setRetreats(0);
      $this->nextState('battleBack', $oppUnit->getPlayer());
    } else {
      $oppUnit->setRetreats(0);
      $this->nextState('takeGround', $attack['pId']);
    }
  }
}
