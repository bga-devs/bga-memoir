<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Units;
use M44\Helpers\Utils;
use M44\Board;

trait TakeGroundTrait
{
  public function stTakeGround()
  {
    $attack = $this->getCurrentAttack();
    $unit = $attack['unit'];
    if (!is_null($unit)) {
      $cell = $unit->getPos();
      $cell['d'] = 0;
    }

    // To take ground, we must have the following:
    //   - attack at distance 1 and cost of deplacement of 1
    //   - Unit is stopped (wire for example)
    //   - attacked cell is now empty (opp unit retreated or eliminated)
    //   - unit has not taken too many grounds already (1 for infantry, 2 for armors)
    //   - unit has not entered a mustStopWhenEntering terrain during the move phase
    //   - unit can enter and take ground on the terrain
    if (
      $attack['distance'] != 1 ||
      is_null($unit) ||
      ($unit->isStopped() && !$unit->canTakeGround()) ||
      Board::getDeplacementCost($unit, $cell, $attack, 1, true) == \INFINITY ||
      $unit->getGrounds() >= $unit->getMaxGrounds() ||
      Board::getUnitInCell($attack['x'], $attack['y']) != null ||
      ($unit->getMoves() > 0 && Board::mustStopWhenEnteringCell($unit->getPos(), $unit)) ||
      Board::isImpassableCell(['x' => $attack['x'], 'y' => $attack['y']], $unit) ||
      Board::cantTakeGroundCell(['x' => $attack['x'], 'y' => $attack['y']], $unit)
    ) {
      $this->closeCurrentAttack();
    }
  }

  public function argsTakeGround()
  {
    $attack = $this->getCurrentAttack();
    return [
      'unitId' => $attack['unitId'],
      'cell' => [
        'x' => $attack['x'],
        'y' => $attack['y'],
      ],
      'actionCount' => Globals::getActionCount(),
    ];
  }

  public function actTakeGround()
  {
    // Sanity checks
    self::checkAction('actTakeGround');
    Globals::incActionCount();

    $player = Players::getCurrent();
    $attack = $this->getCurrentAttack();
    // Move unit
    $unit = $attack['unit'];
    Notifications::takeGround($player, $attack['unitId'], $attack['x'], $attack['y'], $unit->getPos());
    list($interrupted, $victory) = Board::moveUnit($unit, $attack, false, true);
    if ($victory) {
      return;
    }
    if ($interrupted) {
      $this->closeCurrentAttack();
      return; // Victory or unit is dead
    }
    $unit->incGrounds(1);

    if ($unit->getGrounds() == $unit->getMaxGrounds()) {
      $this->closeCurrentAttack();
    } else {
      Globals::setUnitAttacker($unit->getId()); // to make sure armor overrun can be done
      if (Globals::isDesert() && $unit->getType() == ARMOR) {
        $this->nextState('desertMove');
      } else {
        if ($unit->getExtraDatas('cannotArmorOverrun') ) {
          $this->closeCurrentAttack();
        }
        else {
          $this->nextState('overrun');
        }
      }
    }
  }

  public function actPassTakeGround()
  {
    // Sanity checks
    self::checkAction('actPassTakeGround');
    Globals::incActionCount();
    Notifications::message(\clienttranslate('${player_name} does not take ground'), [
      'player' => Players::getCurrent(),
    ]);

    $this->closeCurrentAttack();
  }

  //////////////////////////////////////////
  //     _  _____ _____  _    ____ _  __
  //    / \|_   _|_   _|/ \  / ___| |/ /
  //   / _ \ | |   | | / _ \| |   | ' /
  //  / ___ \| |   | |/ ___ \ |___| . \
  // /_/   \_\_|   |_/_/   \_\____|_|\_\
  //////////////////////////////////////////

  public function stArmorOverrun()
  {
    $args = $this->argsArmorOverrun();
    $nTargets = 0;
    foreach ($args['units'] as $uId => $targets) {
      $nTargets += count($targets);
    }
    if ($nTargets == 0) {
      $this->closeCurrentAttack();
    }
  }

  public function argsArmorOverrun()
  {
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    $attack = $this->getCurrentAttack();
    $args = $card->getArgsArmorOverrun($attack['unitId']);
    Utils::clearPaths($args['units']);
    $args['actionCount'] = Globals::getActionCount();
    return $args;
  }

  // DESERT rules
  public function argsDesertMove()
  {
    $unit = $this->getCurrentAttack()['unit'];
    $moves = $unit->getMoves();

    return [
      'units' => [$unit->getId() => $unit->getPossibleMoves($moves + 1, $moves + 1, true, true)],
      'lastUnitMoved' => $unit->getId(),
      'actionCount' => Globals::getActionCount(),
    ];
  }

  public function stDesertMove()
  {
    $args = $this->argsDesertMove();
    if (count($args['units'][$args['lastUnitMoved']]) == 0) {
      $this->actMoveUnitsDone();
    }
  }
}
