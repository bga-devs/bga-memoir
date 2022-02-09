<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Units;
use M44\Helpers\Utils;
use M44\Board;

trait DrawCardsTrait
{
  public function stDrawCard()
  {
    // draw x cards based on played card
    // transition to choice if more than 1
  }

  public function argsDrawChoice()
  {
    return [];
  }

  public function actChooseCard($cardId)
  {
    // keep this card, remove the others
  }
}
