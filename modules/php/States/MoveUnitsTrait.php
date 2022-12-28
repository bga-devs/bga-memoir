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
  public function argsMoveUnits($player = null, $clearPaths = true)
  {
    $player = $player ?? Players::getActive();
    $card = $player->getCardInPlay();
    $args = $card->getArgsMoveUnits();
    Utils::clearPaths($args['units'], $clearPaths);
    $args['lastUnitMoved'] = Globals::getUnitMoved();
    $args['actionCount'] = Globals::getActionCount();

    // Clear attack highlights for second move of BehindEnemyLines
    if ($this->gamestate->state_id() == \ST_MOVE_AGAIN) {
      foreach ($args['units'] as &$cells) {
        foreach ($cells as &$cell) {
          $cell['canAttack'] = false;
        }
      }
    }

    return $args;
  }

  public function actMoveUnit($unitId, $x, $y)
  {
    // Sanity checks
    self::checkAction('actMoveUnit');
    Globals::incActionCount();
    $player = Players::getCurrent();
    $desertMove = $this->gamestate->state()['name'] == 'desertMove' ? true : false;
    if ($this->gamestate->state()['name'] == 'desertMove') {
      $args = $this->argsDesertMove();
    } else {
      $args = $this->argsMoveUnits($player, false);
    }
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
    if (is_null($cell['paths'])) {
      throw new \BgaVisibleSystemException(
        'Should not happen. Please create a bug report at this exact point in the game with details on what you were trying to do'
      );
    }
    usort($cell['paths'], function ($path1, $path2) {
      return $path1['resistance'] - $path2['resistance'];
    });
    $path = $cell['paths'][0]; // Take the least resistance path
    $unit = Units::get($unitId);
    $coordSource = $unit->getPos();
    $initMove = $unit->getMoves();

    $card = $unit->getActivationOCard();
    $skipRestrictions = $card != null && $card->isType(CARD_BEHIND_LINES) && $unit->getType() == INFANTRY;

    foreach ($path['cells'] as $c) {
      if (!$skipRestrictions) {
        if (Board::mustStopWhenLeavingCell($coordSource, $unit) || Board::mustStopMovingWhenEnteringCell($c, $unit)) {
          $unit->mustStop();
          $unit->ableTakeGround();
        } elseif (Board::mustStopWhenEnteringCell($c, $unit)) {
          $unit->mustStop();
        }
      }

      if (!$desertMove) {
        // do not inc moves if desert moves
        $unit->incMoves($c['cost'] ?? 1);
      }
      Notifications::moveUnit($player, $unit, $coordSource, $c);
      list($interrupted, $isWinning) = Board::moveUnit($unit, $c);
      if ($isWinning) {
        return;
      } elseif ($interrupted) {
        Globals::setUnitMoved($unitId);

        if ($desertMove) {
          $this->nextState('overrun');
          return;
        }
        $this->nextState('moveUnits');
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

    if (isset($cell['teleportation']) && $cell['teleportation'] == true) {
      // if teleported, force move at 1 and unit moved, ==> unit is no more available
      $unit->setMoves(1);
      Globals::setUnitMoved(9999);
    }

    if ($this->gamestate->state()['name'] == 'desertMove') {
      $this->nextState('overrun');
      return;
    }
    $this->nextState('moveUnits');
  }

  public function actMoveUnitsDone($check = true)
  {
    if ($check) {
      self::checkAction('actMoveUnitsDone');
      Globals::incActionCount();
    }

    if ($this->gamestate->state()['name'] == 'desertMove') {
      $this->nextState('overrun');
      return;
    }

    $this->nextState('attackUnits');
  }

  public function stMoveUnits()
  {
    $player = Players::getActive();
    $args = $this->argsMoveUnits($player);
    foreach ($args['units'] as $unitId => $cells) {
      if (count($cells) > 1) {
        return;
      }
    }
    $this->nextState('attackUnits');
  }
}
