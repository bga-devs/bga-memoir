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

    // if ($attack['distance'] != 1) {
    //   throw new \feException('titi 1');
    // }
    // if (is_null($unit)) {
    //   throw new \feException('toto2');
    // }
    // if (Board::getDeplacementCost($unit, $cell, $attack, 1, true) == \INFINITY) {
    //   throw new \feException('tata3');
    // }
    // if ($unit->getGrounds() >= $unit->getMaxGrounds()) {
    //   throw new \feException('truc 4');
    // }
    // if (Board::getUnitInCell($attack['x'], $attack['y']) != null) {
    //   throw new \feException('machin 5');
    // }
    // if ($unit->getMoves() > 0 && Board::mustStopWhenEntering($unit, $unit->getPos())) {
    //   throw new \feException('bidule 6');
    // }
    // if (Board::isImpassableCell(['x' => $attack['x'], 'y' => $attack['y']], $unit)) {
    //   throw new \feException('crotte');
    // }

    // To take ground, we must have the following:
    //   - attack at distance 1 and cost of deplacement of 1
    //   - attacked cell is now empty (opp unit retreated or eliminated)
    //   - unit has not taken too many grounds already (1 for infantry, 2 for armors)
    //   - unit has not entered a mustStopWhenEntering terrain during the move phase
    //   - unit can enter and take ground on the terrain
    if (
      $attack['distance'] != 1 ||
      is_null($unit) ||
      Board::getDeplacementCost($unit, $cell, $attack, 1, true) == \INFINITY ||
      $unit->getGrounds() >= $unit->getMaxGrounds() ||
      Board::getUnitInCell($attack['x'], $attack['y']) != null ||
      ($unit->getMoves() > 0 && Board::mustStopWhenEntering($unit, $unit->getPos())) ||
      Board::isImpassableCell(['x' => $attack['x'], 'y' => $attack['y']], $unit)
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
    ];
  }

  public function actTakeGround()
  {
    // Sanity checks
    self::checkAction('actTakeGround');

    $player = Players::getCurrent();
    $attack = $this->getCurrentAttack();
    // Move unit
    $unit = $attack['unit'];
    Notifications::takeGround($player, $attack['unitId'], $attack['x'], $attack['y'], $unit->getPos());
    list($interrupted, $victory) = Board::moveUnit($unit, $attack);
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
        $this->nextState('overrun');
      }
    }
  }

  public function actPassTakeGround()
  {
    // Sanity checks
    self::checkAction('actPassTakeGround');
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
    if (empty($args['units'])) {
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
    return $args;
  }

  // DESERT rules
  public function argsDesertMove()
  {
    $unit = $this->getCurrentAttack()['unit'];
    $moves = $unit->getMoves();

    return [
      'units' => [$unit->getId() => $unit->getPossibleMoves($moves + 1, $moves + 1, false)],
      'lastUnitMoved' => $unit->getId(),
    ];
  }
}
