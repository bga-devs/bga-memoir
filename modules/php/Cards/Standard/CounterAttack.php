<?php
namespace M44\Cards\Standard;

use M44\Managers\Players;
use M44\Managers\Units;
use M44\Core\Globals;
use M44\Managers\Cards;
use M44\Core\Game;

class CounterAttack extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_COUNTER_ATTACK;
    $this->name = clienttranslate('CounterAttack');
    $this->text = [
      clienttranslate('Issue the same order just played by your opponent.'),
      clienttranslate(
        'When countering a section card, a right section card becomes the equivalent left section card, and vice-versa.'
      ),
      clienttranslate(
        'When counter-attacking an Infantry Assault card, the counter-attack must occur in the same section as your opponent.'
      ),
    ];
  }

  public function getOpponentPId()
  {
    return Players::getNextId($this->pId);
  }

  public function getCopiedCard()
  {
    $lastCards = Globals::getLastPlayedCards();
    $oppId = $this->getOpponentPId();
    $cardId = $lastCards[$oppId] ?? null;
    if (!is_null($cardId)) {
      $card = Cards::get($cardId);
      $card->setCounterAttack($this->pId, $this->id);
      return $card;
    } else {
      return null;
    }
  }

  public function nextStateAfterPlay()
  {
    return 'counterAttack';
  }

  public function stCounterAttack()
  {
    $player = $this->getPlayer();
    $copiedCard = $this->getCopiedCard();
    $transition = is_null($copiedCard) ? 'draw' : $copiedCard->nextStateAfterPlay();
    Game::get()->nextState($transition);
  }

  public function cannotIgnoreFlags()
  {
    return $this->getCopiedCard()->cannotIgnoreFlags();
  }

  public function getDrawMethod()
  {
    $copiedCard = $this->getCopiedCard();
    return is_null($copiedCard) ? parent::getDrawMethod() : $copiedCard->getDrawMethod();
  }

  public function getDiceModifier($unit, $cell)
  {
    return $this->getCopiedCard()->getDiceModifier($unit, $cell);
  }

  public function getHits($type, $nb)
  {
    return $this->getCopiedCard()->getHits($type, $nb);
  }

  public function getArgsOrderUnits()
  {
    return $this->getCopiedCard()->getArgsOrderUnits($this->pId);
  }

  public function getAdditionalPlayConstraints()
  {
    return $this->getCopiedCard()->getAdditionalPlayConstraints();
  }

  public function nextStateAfterOrder($unitIds, $onTheMoveIds)
  {
    return $this->getCopiedCard()->nextStateAfterOrder($unitIds, $onTheMoveIds);
  }

  public function getArgsMoveUnits()
  {
    return $this->getCopiedCard()->getArgsMoveUnits();
  }

  public function getArgsAttackUnits($overrideNbFights = null)
  {
    return $this->getCopiedCard()->getArgsAttackUnits($overrideNbFights);
  }

  public function nextStateAfterAttacks()
  {
    return $this->getCopiedCard()->nextStateAfterAttacks();
  }

  public function getArgsArmorOverrun($unitId)
  {
    return $this->getCopiedCard()->getArgsArmorOverrun($unitId);
  }

  public function getExtraDatas($variable)
  {
    return $this->getCopiedCard()->extraDatas[$variable] ?? null;
  }

  // public function setExtraDatas($variable, $value)
  // {
  //   return $this->getCopiedCard()->setExtraDatas($variable, $value);
  // }

  public function __call($method, $args)
  {
    $card = $this->getCopiedCard();
    if (is_null($card)) {
      throw new \feException("Trying to call $method on CounterAttack without any copied card, Should not happen");
    }

    if (!\method_exists($card, $method)) {
      throw new \feException("Trying to call unexistant $method on copied card of CounterAttack, Should not happen");
    }

    return $card->$method(...$args);
  }
}
