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

  public function onPlay()
  {
    $this->stCounterAttack(false);
  }

  public function nextStateAfterPlay()
  {
    $card = $this->getCopiedCard();
    if (!is_null($card)) {
      $card->setExtraDatas('hill317', $this->getExtraDatas('hill317'));
    }
    return is_null($card) ? 'counterAttack' : $card->nextStateAfterPlay();
  }

  public function canHill317()
  {
    $lastCards = Globals::getLastPlayedCards();
    $oppId = $this->getOpponentPId();
    $cardId = $lastCards[$oppId] ?? null;
    $copiedCard = $this->getCopiedCard($cardId);

    return is_null($copiedCard) ? false : $copiedCard->canHill317();
  }

  // STATE SHOULD BE USELESS NOW => MOVE TO ONPLAY INSTEAD
  public function stCounterAttack($shouldTransition = true)
  {
    // Compute and store the copied card
    $lastCards = Globals::getLastPlayedCards();
    $oppId = $this->getOpponentPId();
    $cardId = $lastCards[$oppId] ?? null;
    $this->setExtraDatas('cardId', $cardId);
    $rawLastCards = Globals::getRawLastPlayedCards();
    $rawCard = $rawLastCards[$oppId] ?? null;
    $this->setExtraDatas('card', $rawCard);

    // Transition to next state depending on copied card
    $transition = 'draw';
    $copiedCard = $this->getCopiedCard();
    if ($copiedCard !== null) {
      $copiedCard->setExtraDatas('hill317', $this->getExtraDatas('hill317'));
      $transition = $copiedCard->nextStateAfterPlay();
    }

    if ($shouldTransition) {
      Game::get()->nextState($transition);
    }
  }

  public function getCopiedCard($forceCard = null)
  {
    $cardId = $this->getExtraDatas('cardId', false);
    if (!is_null($forceCard) && is_null($cardId)) {
      $cardId = $forceCard;
    }
    if (!is_null($cardId)) {
      $rawCard = $this->getExtraDatas('card', false);
      if (false && !is_null($rawCard) && isset($rawCard['id'])) {
        $locations = explode('_', $rawCard['location']);
        $rawCard['player_id'] = $locations[1] ?? null;
        $card = Cards::getInstance($rawCard['type'], $rawCard);
      } else {
        $card = Cards::get($cardId); // FALLBACK CODE => TO REMOVE
      }
      $this->setExtraDatas('CopiedCardType',$card->getExtraDatas('CopiedCardType'));
      if ($card->getType() == \CARD_COUNTER_ATTACK) {
        $this->setExtraDatas('CopiedCardType',$this->getExtraDatas('CopiedCardType')); 
      } else {
        $this->setExtraDatas('CopiedCardType', $card->getType());
      }
      $card->setCounterAttack($this->pId, $this->getId(), $this->isCounterAttackMirror);
      return $card;
    } else {
      return null;
    }
  }

  public function cannotIgnoreFlags()
  {
    return $this->getCopiedCard()->cannotIgnoreFlags();
  }

  public function isType($cardType)
  {
    return $this->getCopiedCard()->isType($cardType);
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

  public function argsTargetAirPower()
  {
    return $this->getCopiedCard()->argsTargetAirPower();
  }

  public function actTargetAirPower($unitIds)
  {
    return $this->getCopiedCard()->actTargetAirPower($unitIds);
  }

  public function stDigIn()
  {
    return $this->getCopiedCard()->stDigIn();
  }

  public function stMoveAgain()
  {
    return $this->getCopiedCard()->stMoveAgain();
  }

  public function stFinestHourRoll()
  {
    return $this->getCopiedCard()->stFinestHourRoll();
  }

  public function argsOrderUnitsFinestHour()
  {
    return $this->getCopiedCard()->argsOrderUnitsFinestHour();
  }

  public function stOrderUnitsFinestHour()
  {
    return $this->getCopiedCard()->stOrderUnitsFinestHour();
  }

  public function actOrderUnitsFinestHour($unitIds)
  {
    return $this->getCopiedCard()->actOrderUnitsFinestHour($unitIds);
  }

  /************ BARRAGE **************/
  public function argsTargetBarrage()
  {
    return $this->getCopiedCard()->argsTargetBarrage();
  }

  public function actTargetBarrage($unitId)
  {
    return $this->getCopiedCard()->actTargetBarrage($unitId);
  }

  /************ MEDICS **************/
  public function argsTargetMedics()
  {
    return $this->getCopiedCard()->argsTargetMedics();
  }

  public function stTargetMedics()
  {
    return $this->getCopiedCard()->stTargetMedics();
  }

  public function actTargetMedics($unitId)
  {
    return $this->getCopiedCard()->actTargetMedics($unitId);
  }

  public function __call($method, $args)
  {
    $card = $this->getCopiedCard();
    if (is_null($card)) {
      throw new \feException("Trying to call $method on CounterAttack without any copied card, Should not happen");
    }

    if (!\method_exists($card, $method)) {
      // throw new \feException($this->counterAttackCardId);
      // throw new \feException(print_r($this));
      throw new \feException("Trying to call unexistant $method on copied card of CounterAttack, Should not happen");
    }

    return $card->$method(...$args);
  }
}
