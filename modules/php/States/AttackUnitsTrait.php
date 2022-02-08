<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Units;
use M44\Helpers\Utils;
use M44\Board;

trait AttackUnitsTrait
{
  public function argsAttackUnit($player = null)
  {
    $player = $player ?? Players::getActive();
    $card = $player->getCardInPlay();
    return [];
    return $card->getArgsMoveUnits();
  }
}
