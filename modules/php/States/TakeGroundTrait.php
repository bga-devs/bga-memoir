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
  /////////////////////////////////
  //  __  __  _____     _______
  // |  \/  |/ _ \ \   / / ____|
  // | |\/| | | | \ \ / /|  _|
  // | |  | | |_| |\ V / | |___
  // |_|  |_|\___/  \_/  |_____|
  /////////////////////////////////
  public function argsTakeGround()
  {
    $currentAttack = Globals::getCurrentAttack();
    return ['unit' => $currentAttack['unitId'], 'x' => $currentAttack['x'], 'y' => $currentAttack['y']];
  }

  public function stTakeGround()
  {
    $currentAttack = Globals::getCurrentAttack();
    $stateName = $this->gamestate->state()['name'];

    if ($stateName == 'armorOverrunMove') {
      $unitType = ARMOR;
    } else {
      $unitType = \INFANTRY;
    }

    if (
      Units::get($currentAttack['unitId'])->getType() != $unitType ||
      $currentAttack['distance'] != 1 ||
      Board::getUnitInCell($currentAttack['x'], $currentAttack['y']) != null
    ) {
      $this->nextState('next');
    }
  }

  public function actTakeGround()
  {
    // Sanity checks
    self::checkAction('actTakeGround');
    $args = $this->argsTakeGround();
    $player = Players::getCurrent();

    $unit = Units::get($args['unit']);
    $unit->moveTo($args);
    Notifications::takeGround($player, $unit->getId(), $args['x'], $args['y']);

    $this->nextState('attack');
  }

  public function actNextAttack()
  {
    // Sanity checks
    self::checkAction('actNextAttack');
    $msg = '';
    if ($this->gamestate->state()['name'] == 'armorOverrunMove') {
      $msg = clienttranslate('${player_name} does not do an armor overrun');
    } elseif ($this->gamestate->state()['name'] == 'armorOverrunAttack') {
      $msg = clienttranslate('${player_name} does not attack');
    }

    Notifications::message($msg, [
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

  public function stArmorOverrunAttack()
  {
    $args = $this->argsAttackUnit();
    $currentAttack = Globals::getCurrentAttack();

    if (count($args['units'][$currentAttack['unitId']]) == 0) {
      $this->nextState('next');
    }
  }

  // public function argsArmorOverrunAttack()
  // {
  //   $currentAttack = Globals::getCurrentAttack();
  //   $args = $this->argsAttackUnit();
  //
  //   return ['units' => [$currentAttack['unitId'] => $args['units'][$currentAttack['unitId']] ?? []]];
  // }
}
