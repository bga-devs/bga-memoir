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
  public function stDrawCard($player = null)
  {
    // Discard played card
    $player = $player ?? Players::getActive();
    $card = $player->getCardInPlay();
    $method = $card->getDrawMethod();
    Cards::discard($card);
    Notifications::discardCard($player, $card);

    if ($method['nKeep'] == $method['nDraw']) {
      $cards = Cards::pickForLocation($method['nDraw'], 'deck', ['hand', $player->getId()]);
      Notifications::drawCards($player, $cards);
      $this->gamestate->nextState('endRound');
    } else {
      $cards = Cards::pickForLocation($method['nDraw'], 'deck', ['choice', $player->getId()]);
      Notifications::drawCardsAndKeep($player, $cards, $method['nKeep']);
      Globals::setNToKeep($method['nKeep']);
      $this->gamestate->nextState('choice');
    }
  }

  /*****
   * Card choice (eg for Recon)
   */
  public function argsDrawChoice()
  {
    $player = Players::getActive();
    $cards = Cards::getInLocation(['choice', $player->getId()])->getIds();
    return [
      'keep' => Globals::getNToKeep(),
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
    Notifications::discardDrawCards($player, $othCards);

    $this->gamestate->nextState('endRound');
  }
}
