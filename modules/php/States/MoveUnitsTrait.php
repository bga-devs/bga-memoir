<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Units;
use M44\Helpers\Utils;
use M44\Board;

trait MoveUnitsTrait
{
  public function argsMoveUnits($player = null, $clearPaths = false)
  {
    $player = $player ?? Players::getActive();
    $card = $player->getCardInPlay();
    $args = $card->getArgsMoveUnits();
    Utils::clearPaths($args['units'], $clearPaths);
    $args['lastUnitMoved'] = Globals::getUnitMoved();
    return $args;
  }

  public function actMoveUnit($unitId, $x, $y)
  {
    // Sanity checks
    self::checkAction('actMoveUnit');
    $player = Players::getCurrent();
    $args = $this->argsMoveUnits($player, false);
    if (!\array_key_exists($unitId, $args['units'])) {
      throw new \BgaVisibleSystemException('You cannot move this unit. Should not happen');
    }
    $cells = $args['units'][$unitId];
    $k = Utils::searchCell($cells, $x, $y);
    if ($k === false) {
      throw new \BgaVisibleSystemException('You cannot move this unit to this hex. Should not happen');
    }

    // Move the unit
    $cell = $cells[$k];
    $path = $cell['paths'][0]; // Take the first path
    $unit = Units::get($unitId);
    $coordSource = $unit->getPos();
    $initMove = $unit->getMoves();
    foreach ($path as $c) {
      if (Board::mustStopWhenLeavingCell($coordSource, $unit)) {
        $unit->mustStop();
      }

      $unit->incMoves($c['cost'] ?? 1);
      Notifications::moveUnit($player, $unit, $coordSource, $c);
      list($interrupted, $isWinning) = Board::moveUnit($unit, $c);
      if ($isWinning) {
        return;
      } elseif ($interrupted) {
        if ($this->gamestate->state()['name'] == 'desertMove') {
          $this->gamestate->nextState('overrun');
          return;
        }
        $this->gamestate->nextState('moveUnits');
        return;
      }
      $coordSource = $c;
    }

    // Handle Road
    if ($cell['road'] ?? false) {
      $unit->useRoadBonus();
    } else {
      $unit->leaveRoad();
    }

    Globals::setUnitMoved($unitId);

    if ($this->gamestate->state()['name'] == 'desertMove') {
      $this->gamestate->nextState('overrun');
      return;
    }
    $this->gamestate->nextState('moveUnits');
  }

  public function actMoveUnitsDone($check = true)
  {
    if ($check) {
      self::checkAction('actMoveUnitsDone');
    }

    if ($this->gamestate->state()['name'] == 'desertMove') {
      $this->gamestate->nextState('overrun');
      return;
    }

    $this->gamestate->nextState('attackUnits');
  }

  public function stMoveUnits()
  {
    $player = Players::getActive();
    $args = $this->argsMoveUnits($player);
    foreach ($args['units'] as $unitId => $cells) {
      if (!empty($cells)) {
        return;
      }
    }
    $this->gamestate->nextState('attackUnits');
  }
}
