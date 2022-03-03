<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Cards;
use M44\Helpers\Utils;
use M44\Board;

trait TacticCardTrait
{
  public function stDigIn()
  {
    $card = Cards::getByType(\CARD_DIG_IN)->first();
    $card->stDigIn();
    $this->nextState('next');
  }
}