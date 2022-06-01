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
      $owner = $otherCard->getPlayer();
      if ($owner == $player) {
        continue;
      }

      $oMethod = $otherCard->getDrawMethod();
      Cards::discard($otherCard);
      Notifications::discardCard($owner, $otherCard, false);

      $newCards = Cards::draw($oMethod['nDraw'], ['hand', $owner->getId()]);
      Notifications::drawCards($owner, $newCards);
    }

    $method = $card->getDrawMethod();

    Cards::discard($card);
    Notifications::discardCard($player, $card, false);

    // TODO : handle the mode where we don't reshuffle (deck exhaustion)
    if ($card->getType() == \CARD_FINEST_HOUR && Globals::getDeckReshuffle()) {
      $n = Cards::reshuffle();
      Notifications::reshuffle($n);
    }

    if ($method['nKeep'] == $method['nDraw']) {
      $cards = Cards::draw($method['nDraw'], ['hand', $player->getId()]);
      if (is_null($cards)) {
        return;
      }
      Notifications::drawCards($player, $cards);
      $this->nextState('endRound');
    } else {
      $cards = Cards::draw($method['nDraw'], ['choice', $player->getId()]);
      if (is_null($cards)) {
        return;
      }
      Notifications::drawCardsAndKeep($player, $cards, $method['nKeep']);
      Globals::setNToKeep($method['nKeep']);
      $this->nextState('choice');
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

  public function actChooseCard($cardId, $choice)
  {
    $this->actChooseCards([$cardId], $choice);
  }

  public function actChooseCards($cardIds, $choice)
  {
    // keep the cards, remove the others
    self::checkAction('actChooseCard');
    Globals::incActionCount();
    $player = Players::getCurrent();
    $args = $this->argsDrawChoice();

    if (count($cardIds) != $args['keep']) {
      // TODO : only handling recon 1/1 card here
      throw new \BgaVisibleSystemException('Number of cards to keep not consistent with rules. Should not happen');
    }

    if (count(array_diff($cardIds, $args['_private']['active']['cards']->getIds())) != 0) {
      throw new \BgaVisibleSystemException('Those cards cannot be selected. Should not happen');
    }

    // If choice is keep, discard the others
    if ($choice == 0) {
      $cardIds = array_diff($player->getCardsChoice()->getIds(), $cardIds);
    }

    // Move selected cards to discard
    foreach ($cardIds as $cardId) {
      Cards::discard((int) $cardId);
    }
    $cards = Cards::get($cardIds);

    // Move other cards to hand
    $otherCards = $player->getCardsChoice();
    Cards::move($otherCards->getIds(), ['hand', $player->getId()]);
    Notifications::discardCard($player, $cards);

    $this->nextState('endRound');
  }
}
