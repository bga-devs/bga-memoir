<?php

namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Units;
use M44\Helpers\Utils;
use M44\Board;
use M44\Core\Game;
use M44\Scenario;


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
    $cells = $args['units'][$unitId] ?? null;
    if (is_null($cells)) {
      throw new \BgaVisibleSystemException('You cannot move this unit. Should not happen');
    }
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
    $trainCase = count($trainIds) > 1 && in_array($unitId, $trainIds);
    if ($trainCase) {
      $firstUnitId[] = $unitId;
      $secondUnitToMoveId = array_diff($trainIds, $firstUnitId);
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
    $skipRestrictions =
      $card != null
      && ($card->isType(CARD_BEHIND_LINES)
        || $card->isType(\CARD_COUNTER_ATTACK)
        && $card->getExtraDatas('copiedCardType') == \CARD_BEHIND_LINES)
      && $unit->getType() == INFANTRY;

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
        if ($trainCase) {
          Units::get($secondUnitToMoveId)->incMoves($c['cost'] ?? 1);
          $c2 = $c;
          $c2['x'] = $coordSource['x'];
          $c2['y'] = $coordSource['y'];
        }
      }
      if ($unit->isOnReserveStaging()) {
        Units::moveFromReserveToBoard($unit);
        // Notification moveUnitFromReserve
        Notifications::moveUnitFromReserve($player,$unit,$coordSource, $c);
      } else {
        Notifications::moveUnit($player, $unit, $coordSource, $c);
      }
      list($interrupted, $isWinning) = Board::moveUnit($unit, $c);
      if ($trainCase) {
        $secondUnitToMove = Units::get($secondUnitToMoveId);
        $secondUnitPos = $secondUnitToMove->getPos();
        Notifications::moveUnitNoMsg($player, $secondUnitToMove, $secondUnitPos, $c2);
        $tmp = Board::moveUnit($secondUnitToMove, $c2);
      }
      if ($isWinning) {
        $this->nextState('endRound');
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
      /* Case if Infantry or unit moved only one hex on road 
      // and wants to move another one and battle on second hex on the road
      if ($unit->getMovesOnTheRoad() == 0) {
        //$unit->useRoadBonus();
        //$unit->incMoves($cell['cost'] - 1);
        $moveOnTheRoad = $unit->getMovesOnTheRoad() + $cell['cost'];
        $unit->setExtraDatas('movesOnTheRoad', $moveOnTheRoad );
      } else {
        //$unit->incMoves($cell['cost']);
        $moveOnTheRoad = $unit->getMovesOnTheRoad() + $cell['cost'];
        $unit->setExtraDatas('movesOnTheRoad', $moveOnTheRoad );
      }*/
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

    if ($unit->getType() == LOCOMOTIVE && $unit->getExtraDatas('trainReinforcement') && !Globals::getSupplyTrainDone()) {
      $this->nextState('trainReinforcement');
    } else {
      $this->nextState('moveUnits');
    }
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

  public function argsTrainReinforcement()
  {
    $train = Units::getAll()->filter(function ($unit) {
      return in_array($unit->getType(), [LOCOMOTIVE, WAGON]) && !$unit->isEliminated();
    });
    $neighbourCells = [];
    foreach ($train as $trainunit) {
      $cells = Board::getNeighbours($trainunit->getPos());
      foreach ($cells as $c) {
        if (!in_array($c, $neighbourCells) && Board::getUnitInCell($c) == null) {
          $neighbourCells[] = $c;
        }
      }
    }
    $args = $neighbourCells;
    return $args;
  }

  public function actTrainReinforcement($x, $y)
  {
    // Sanity checks
    self::checkAction('actTrainReinforcement');
    $args = $this->argsTrainReinforcement();
    $k = Utils::searchCell($args, $x, $y);
    if ($k === false) {
      throw new \BgaVisibleSystemException('You cannot performed reinforcement here. Should not happen');
    }

    $player = Players::getCurrent();
    $train = Units::getAll()->filter(function ($unit) {
      return in_array($unit->getType(), [LOCOMOTIVE, WAGON]) && !$unit->isEliminated();
    });
    Globals::incReinforcementUnits();
    $options = Scenario::getOptions()['supply_train_rules'];
    $currentTurn = Globals::getTurn();
    $reinforcementNumber = Globals::getReinforcementUnits();
    $maxi = $options['nbr_units'][$reinforcementNumber - 1];

    for ($i = 0; $i < $maxi; $i++) {
      $pos = ['x' => $x, 'y' => $y];
      $unit = Units::addInCell($options['unit'], $pos);
      if (
        isset($options['unit']['behavior'])
        && $options['unit']['behavior'] == 'CANNOT_BE_ACTIVATED_TILL_TURN'
      ) {
        $unit->setExtraDatas('cannotBeActivatedUntilTurn', $options['unit']['turn'] + $currentTurn);
      }
      Board::addUnit($unit);
      Notifications::trainReinforcement($player, $unit);
    }

    if (Globals::getReinforcementUnits() >= count($train)) {
      Globals::setReinforcementUnits(0);
      Globals::setSupplyTrainDone(true);
      $this->gamestate->jumpToState(ST_MOVE_UNITS);
    } else {
      $this->gamestate->jumpToState(ST_TRAIN_REINFORCEMENT);
    }
  }
}
