<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Units;
use M44\Helpers\Utils;
use M44\Board;

trait OverrunTrait
{
  public function argsArmorOverrunMove()
  {
  }

  public function stArmorOverrun()
  {
    $currentAttack = Globals::getCurrentAttack();
    if (
      Units::get($currentAttack['unitId'])->getType() != \ARMOR ||
      $currentAttack['distance'] != 1 ||
      Board::getUnitInCell($currentAttack['x'], $currentAttack['y']) != null
    ) {
      $this->nextState('next');
    }
  }
}
