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

  public function stMoveAgain()
  {
    $card = Cards::getByType(\CARD_BEHIND_LINES)->first();
    $card->stMoveAgain();
    $this->nextState('next');
  }

  public function stFinestHourRoll()
  {
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    $card->stFinestHourRoll();
  }

  public function stOrderUnitsFinestHour()
  {
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    return $card->stOrderUnitsFinestHour();
  }
  public function argsOrderUnitsFinestHour()
  {
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    return $card->argsOrderUnitsFinestHour();
  }

  public function actOrderUnitsFinestHour($unitIds)
  {
    self::checkAction('actOrderUnitsFinestHour');
    $player = Players::getCurrent();
    $card = $player->getCardInPlay();
    return $card->actOrderUnitsFinestHour($unitIds);
  }

  /************ AIR POWER **************/
  public function argsTargetAirPower()
  {
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    return $card->argsTargetAirPower();
  }

  public function actTargetAirPower($unitIds)
  {
    self::checkAction('actTargetAirPower');
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    return $card->actTargetAirPower($unitIds);
  }

  /************ BARRAGE **************/
  public function argsTargetBarrage()
  {
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    return $card->argsTargetBarrage();
  }

  public function actTargetBarrage($unitId)
  {
    self::checkAction('actTargetBarrage');
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    return $card->actTargetBarrage($unitId);
  }

  /************ MEDICS **************/
  public function argsTargetMedics()
  {
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    return $card->argsTargetMedics();
  }

  public function stTargetMedics()
  {
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    return $card->stTargetMedics();
  }

  public function actTargetMedics($unitId)
  {
    self::checkAction('actTargetMedics');
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    return $card->actTargetMedics($unitId);
  }

  /************ COUNTER ATTACK **********/
  public function stCounterAttack()
  {
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    return $card->stCounterAttack();
  }
}
