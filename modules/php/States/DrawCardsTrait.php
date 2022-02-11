<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Units;
use M44\Managers\Cards;
use M44\Helpers\Utils;
use M44\Board;

trait DrawCardsTrait
{
  public function stDrawCard()
  {
    $choice = false;
    $player = $player ?? Players::getActive();
    $card = $player->getCardInPlay();
    $method = $card->getDrawMethod();
    if ($method['keep'] != $method['draw']) {
      $choice = true;
    }

    if (!$choice) {
      $cards = Cards::pickForLocation($method['draw'], 'deck', ['hand', $player->getId()]);
    } else {
      $cards = Cards::pickForLocation($method['draw'], 'deck', ['choice', $player->getId()]);
    }
    Notifications::drawCards($player, $cards);
    // transition to choice if more than 1
    if (!$choice) {
      $this->gamestate->nextState('endRound');
    } else {
      $this->gamestate->nextState('choice');
    }
  }

  public function argsDrawChoice()
  {
    $player = Players::getActive();
    $cards = Cards::getInLocation(['choice', $player->getId()])->getIds();
    return [
      'keep' => $player->getCardInPlay()->getDrawMethod()['keep'],
      '_private' => [
        'active' => [
          'cards' => $cards,
        ],
      ],
    ];
  }

  public function actChooseCard($cardIds)
  {
    // keep the cards, remove the others
    self::checkAction('actChooseCard');
    $player = Players::getCurrent();
    $args = $this->argsDrawChoice();

    if (count($cardIds) != $args['keep']) {
      throw new \BgaVisibleSystemException('Number of cards to keep not consistent with rules. Should not happen');
    }

    if (count(array_diff($cardIds, $args['_private']['active']['cards'])) != 0) {
      throw new \BgaVisibleSystemException('Those cards cannot be selected. Should not happen');
    }

    Cards::move($cardIds, ['hand', $player->getId()]);
    $cards = Cards::getMany($cardIds);
    Notifications::drawCards($player, $cards);

    $othCards = Cards::getInLocation(['choice', $player->getId()])->getIds();
    $cards = Cards::move($othCards, 'discard');
    Notifications::discard($player, $othCards, false);

    $this->gamestate->nextState('endRound');
  }
}
