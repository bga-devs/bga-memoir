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
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    $card->stDigIn();
    $this->nextState('next');
  }

  public function stMoveAgain()
  {
    $player = Players::getActive();
    $card = $player->getCardInPlay();
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
    Globals::incActionCount();
    $player = Players::getCurrent();
    $card = $player->getCardInPlay();
    return $card->actOrderUnitsFinestHour($unitIds);
  }

  /************ AIR POWER **************/
  public function argsTargetAirPower()
  {
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    $args = $card->argsTargetAirPower();
    $args['actionCount'] = Globals::getActionCount();
    return $args;
  }

  public function actTargetAirPower($unitIds)
  {
    self::checkAction('actTargetAirPower');
    Globals::incActionCount();
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    return $card->actTargetAirPower($unitIds);
  }

  /************ BARRAGE **************/
  public function argsTargetBarrage()
  {
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    $args = $card->argsTargetBarrage();
    $args['actionCount'] = Globals::getActionCount();
    return $args;
  }

  public function actTargetBarrage($unitId)
  {
    self::checkAction('actTargetBarrage');
    Globals::incActionCount();
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    return $card->actTargetBarrage($unitId);
  }

  /************ MEDICS **************/
  public function argsTargetMedics()
  {
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    $args = $card->argsTargetMedics();
    $args['actionCount'] = Globals::getActionCount();
    return $args;
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
    Globals::incActionCount();
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


  /************* MEDICS BT ***************/
  public function stMedicsBTRoll()
  {
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    return $card->stMedicsBTRoll();
  }

  public function argsMedicsBTHeal()
  {
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    return $card->argsMedicsBTHeal();
  }
}
