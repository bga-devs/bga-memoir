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

  public function getCopiedCard()
  {
    $last = Globals::getLastPlayedCards();
    $player = $this->getPlayer();
    foreach ($last as $pId => $cardId) {
      if ($pId != $player->getId()) {
        return Cards::get($cardId);
      }
    }

    return null;
  }

  public function nextStateAfterPlay()
  {
    return 'counterAttack';
  }

  public function stCounterAttack()
  {
    $player = $this->getPlayer();
    $copiedCard = $this->getCopiedCard();
    if ($copiedCard == null) {
      Game::get()->nextState('draw');
      return;
    }
    // trigger next play
    Game::get()->nextState($copiedCard->nextStateAfterPlay());
  }

  public function cannotIgnoreFlags()
  {
    return $this->getCopiedCard()->cannotIgnoreFlags();
  }

  public function getDrawMethod()
  {
    $copied = $this->getCopiedCard();
    if ($copied == null) {
      return parent::getDrawMethod();
    }
    return $copied->getDrawMethod();
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
    $args = $this->getCopiedCard()->getArgsOrderUnits($this->pId, true);
    // TODO: infantry assault?
    return $args;
  }

  public function nextStateAfterOrder($unitIds, $onTheMoveIds)
  {
    return $this->getCopiedCard()->nextStateAfterOrder($unitIds, $onTheMoveIds);
  }

  public function getArgsMoveUnits()
  {
    return $this->getCopiedCard()->getArgsMoveUnits();
  }

  // public function getAdditionalPlayConstraints()
  // {
  //   return $this->getCopiedCard()->getAdditionalPlayConstraints();
  // }

  /**
   *
   * @param $overrideNbFights = [UNIT_TYPE => maxFights]]
   *
   **/
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

  // section Cards
  public function getSections()
  {
    return array_reverse($this->getCopiedCard()->getSections());
  }
}
