<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Cards;
use M44\Managers\Units;
use M44\Core\Notifications;

trait AmbushTrait
{
  function argsOpponentAmbush()
  {
    $player = Players::getActive();
    $cards = $player->getCards();
    $cards = $cards
      ->filter(function ($card) {
        return $card->getType() == CARD_AMBUSH;
      })
      ->getIds();
    return ['_private' => ['active' => ['cards' => $cards]]];
  }

  function stAmbush()
  {
    // check if ambush viable else pass
    $currentAttack = Globals::getCurrentAttack();

    if ($currentAttack['distance'] != 1) {
      $this->actPassAmbush(true);
    }
  }

  function actAmbush()
  {
  }

  function actPassAmbush($silent = false)
  {
    if (!$silent) {
      Notifications::message(clienttranslate('${player_name} does not react to the attack'), [
        'player' => Players::getActive(),
      ]);
    }
    $this->nextState('pass', Globals::getActivePlayer());
  }
}
