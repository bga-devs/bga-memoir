<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Troops;

trait MoveUnitsTrait
{
  public function argsMoveUnits()
  {
    $player = $player ?? Players::getActive();
    $card = $player->getCardInPlay();
    return $card->getArgsMoveUnits();
  }
}
