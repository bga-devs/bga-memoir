<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Units;
use M44\Helpers\Utils;
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
    $attackedUnit = Units::get($attack['oppUnitId']);
    // TODO : compute the min/max flags

    Globals::setRetreat([
      'min' => $canIgnoreAllFlags ? 0 : $dice[\DICE_FLAG] - ($canIgnore1Flag ? 1 : 0),
      'max' => $dice[\DICE_FLAG] * $attackedUnit->getRetreatHex(),
      'unit' => $attack['oppUnitId'],
    ]);
  }

  /**
   * Fetch retreat relevant informations
   */
  public function getRetreatInfo()
  {
    $data = Globals::getRetreat();
    // $attack = $this->getCurrentAttack();
    return [Units::get($data['unit']), $data['min'], $data['max']];
  }

  /**
   * Automatically resolve state if only one possible choice
   */
  public function stRetreatUnit()
  {
    // Take the hits if any
    $args = $this->argsRetreatUnit();
    if ($args['hits'] > 0) {
      $unit = Units::get($args['unitId']);
      $eliminated = $this->damageUnit($unit, $args['hits'], true);
      $retreatInfo = Globals::getRetreat();
      $retreatInfo['min'] -= $args['hits'];
      Globals::setRetreat($retreatInfo);
      if (Teams::checkVictory()) {
        return;
      }

      if ($this->getCurrentAttack()['unit'] != null) {
        $this->getCurrentAttack()['unit']->afterAttackRetreatHit($unit->getPos(), $args['hits'], $eliminated);
      }

      $this->nextState('retreat');
    }
    // If no more cells, auto-done
    elseif (empty($args['cells'])) {
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
    list($unit, $minFlags, $maxFlags) = $this->getRetreatInfo();
    $args = array_merge(Board::getArgsRetreat($unit, $minFlags, $maxFlags), [
      'unitId' => $unit->getId(),
      'min' => $minFlags,
      'max' => $maxFlags,
      'desc' => $minFlags == $maxFlags ? '' : \clienttranslate('(up to ${max} cells)'),
      'i18n' => ['desc'],
      'titleSuffix' => $minFlags == 0 ? 'skippable' : false,
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
    list(, $minFlags) = $this->getRetreatInfo();
    if ($minFlags > 0) {
      throw new \BgaUserException(clienttranslate('You did not retreat far enough. Should not happen.'));
    }

    $attack = $this->getCurrentAttack();
    if ($attack['unit'] == null) {
      // Attack triggered by a card without order
      $this->closeCurrentAttack();
    } else {
      $this->nextState('takeGround', $attack['pId']);
    }
  }
}
