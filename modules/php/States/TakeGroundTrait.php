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

    // To take ground, we must have the following:
    //   - attack at distance 1
    //   - attacked cell is now empty (opp unit retreated or eliminated)
    //   - unit has not taken too many grounds already (1 for infantry, 2 for armors)
    //   - unit has not entered a mustStopWhenEntering terrain during the move phase
    //   - unit can enter on the terrain
    //   - unit is not trying to take ground from a beach on a cliff
    if (
      $attack['distance'] != 1 ||
      $unit->getGrounds() >= $unit->getMaxGrounds() ||
      Board::getUnitInCell($attack['x'], $attack['y']) != null ||
      ($unit->getMoves() > 0 && Board::mustStopWhenEntering($unit, $unit->getPos())) ||
      Board::isImpassable($unit, ['x' => $attack['x'], 'y' => $attack['y']]) ||
      ($unit->getType() == \INFANTRY &&
        Board::cellHasProperty(['x' => $attack['x'], 'y' => $attack['y']], 'isCliff', $unit) &&
        Board::isBeach($unit->getPos()))
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
    Notifications::takeGround($player, $attack['unitId'], $attack['x'], $attack['y']);
    $interrupted = Board::moveUnit($unit, $attack);
    if ($interrupted) {
      return; // Victory or unit is dead
    }
    $unit->incGrounds(1);
    $this->nextState('overrun');
  }

  public function actPassTakeGround()
  {
    // Sanity checks
    self::checkAction('actPassTakeGround');
    Notifications::message(\clienttranslate('${player_name} does not take ground'), [
      'player' => Players::getCurrent(),
    ]);
    $this->nextState('next');
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
}
