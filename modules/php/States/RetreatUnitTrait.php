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

    if ($currentAttack['card']->cannotIgnoreFlags()) {
      $canIgnore1Flag = false;
      $canIgnoreAllFlags = false;
    }

    if ($oppUnit->getMustIgnore1Flag()) {
      $dice[\DICE_FLAG]--;
      if ($canIgnore1Flag && $dice[\DICE_FLAG] != 0) {
        $dice[\DICE_FLAG]--;
        $canIgnore1Flag = false;
      }
    }
    $attackedUnit = Units::get($attack['oppUnitId']);
    // TODO : compute the min/max flags

    Globals::setRetreat([
      'min' => $canIgnoreAllFlags ? 0 : $dice[\DICE_FLAG] - ($canIgnore1Flag ? 1 : 0),
      'max' => $dice[\DICE_FLAG] * $attackedUnit->getRetreatHex(),
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
    if ($args['hits'] > 0) {
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
  }

  /**
   * Compute the units that can attack
   */
  public function argsRetreatUnit($clearPaths = false)
  {
    $player = Players::getActive();
    list($unit, $minFlags, $maxFlags, $effect) = $this->getRetreatInfo();
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
      'titleSuffix' => $effect == '' ? ($minFlags == 0 ? 'skippable' : false) : $effect,
    ]);
    Utils::clearPaths($args['units'], $clearPaths); // Remove paths, useless for UI
    return $args;
  }

  public function actRetreatUnit($x, $y, $auto = false)
  {
    // Sanity checks
    if (!$auto) {
      self::checkAction('actRetreatUnit');
    }
    $args = $this->argsRetreatUnit();
    $player = Players::getCurrent();
    $cells = $args['cells'];
    $k = Utils::searchCell($cells, $x, $y);
    if ($k === false) {
      throw new \BgaVisibleSystemException('You cannot move this unit to this hex. Should not happen');
    }

    // Move the unit
    $cell = $cells[$k];
    $dist = $cell['d'];
    $path = $cell['paths'][0]; // Take the first path
    $unitId = $args['unitId'];
    $unit = Units::get($unitId);
    $coordSource = $unit->getPos();
    foreach ($path as $c) {
      Notifications::retreatUnit($player, $unit, $coordSource, $c);
      list($interrupted, $isWinning) = Board::moveUnit($unit, $c, true);
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
    }
    // check that retreat = 0
    list($unit, $minFlags, , $effect) = $this->getRetreatInfo();
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
      $oppUnit->getNUnits() == 1 &&
      $oppUnit->getRetreats() == 0
    ) {
      $this->nextState('battleBack', $oppUnit->getPlayer());
    } else {
      $this->nextState('takeGround', $attack['pId']);
    }
  }
}
