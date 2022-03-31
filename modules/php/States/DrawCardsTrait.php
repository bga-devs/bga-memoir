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

    // Ambush card management
    $cards = Cards::getInPlayOfAll();
    foreach ($cards as $otherCard) {
      if ($otherCard->getPlayer() == $player) {
        continue;
      }

      $owner = $otherCard->getPlayer();
      $oMethod = $otherCard->getDrawMethod();
      Cards::discard($otherCard);
      Notifications::discardCard($owner, $otherCard);

      $newCards = Cards::pickForLocation($oMethod['nDraw'], 'deck', ['hand', $owner->getId()]);
      Notifications::drawCards($owner, $newCards);
    }

    $method = $card->getDrawMethod();

    Cards::discard($card);
    Notifications::discardCard($player, $card);

    // TODO : handle the mode where we don't reshuffle (deck exhaustion)
    if ($card->getType() == \CARD_FINEST_HOUR && Globals::getDeckReshuffle()) {
      $n = Cards::reshuffle();
      Notifications::reshuffle($n);
    }

    if ($method['nKeep'] == $method['nDraw']) {
      $cards = Cards::pickForLocation($method['nDraw'], 'deck', ['hand', $player->getId()]);
      if (is_null($cards)) {
        return;
      }
      Notifications::drawCards($player, $cards);
      $this->gamestate->nextState('endRound');
    } else {
      $cards = Cards::pickForLocation($method['nDraw'], 'deck', ['choice', $player->getId()]);
      if (is_null($cards)) {
        return;
      }
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
    $cards = Cards::getInLocation(['choice', $player->getId()]);
    return [
      'keep' => Globals::getNToKeep(),
      '_private' => [
        'active' => [
          'cards' => $cards,
        ],
      ],
    ];
  }

  public function actChooseCard($cardId)
  {
    $this->actChooseCards([$cardId]);
  }

  public function actChooseCards($cardIds)
  {
    // keep the cards, remove the others
    self::checkAction('actChooseCard');
    $player = Players::getCurrent();
    $args = $this->argsDrawChoice();

    if (count($cardIds) != $args['keep']) {
      throw new \BgaVisibleSystemException('Number of cards to keep not consistent with rules. Should not happen');
    }

    if (count(array_diff($cardIds, $args['_private']['active']['cards']->getIds())) != 0) {
      throw new \BgaVisibleSystemException('Those cards cannot be selected. Should not happen');
    }

    // Move selected cards to hand
    Cards::move($cardIds, ['hand', $player->getId()]);
    // Discard other cards
    $otherCards = $player->getCardsChoice();
    foreach ($otherCards as $card) {
      Cards::discard($card);
    }
    Notifications::discardCards($player, $otherCards);

    $this->gamestate->nextState('endRound');
  }
}
