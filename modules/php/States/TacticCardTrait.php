<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Cards;
use M44\Helpers\Utils;
use M44\Board;
use M44\Dice;
use M44\Managers\Terrains;

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

  public function actMedicsBTHeal($unitIds)
  {
    self::checkAction('actMedicsBTHeal');
    Globals::incActionCount();
    $player = Players::getCurrent();
    $card = $player->getCardInPlay();
    return $card->actMedicsBTHeal($unitIds);
  }

  /************* BLOW BRIDGE ***************/
  public function stBlowBridge()
  {
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    $this->nextState('next');
  }

  public function argsblowbridge()
  {
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    $cardsections = $card->getSections();
    $side = Globals::getTeamTurn();
    // TODO option : if an allied unit should be close to the bridge
    $terrains = Terrains::getAll(); //OK
    $bridges = $terrains->filter(function ($t) {
      return $t instanceof \M44\Terrains\Bridge
      || $t instanceof \M44\Terrains\RailroadBridge;
    });
    $bridges = array_filter($bridges->toArray(), fn($t) => 
      $this->isTerrainInCardSections($t, $cardsections, $side)
    );
    return ['terrains' => $bridges] ;
  }

  public function isTerrainInCardSections($terrain, $cardsections, $side) {
    $isInSection = false;
    foreach($terrain->getSections($side) as $s) {
      if(!$isInSection) {
        $isInSection = ($cardsections[$s]!=0);
      }
    }
    return $isInSection;
  }

  public function actBlowBridge($terrainId)
  {
    self::checkAction('actBlowBridge');
    Globals::incActionCount();

    $args = self::argsblowbridge();
   
    // TO DO get terrain by Id $terrain->getId() ou direct DB DB()->delete($terrainId);
    $selectedBridge = Terrains::get($terrainId);
    var_dump($terrainId);

    $player = Players::getCurrent();

    // Roll 2 dices, on star, destroy bridge
    $results = Dice::roll($player, 2);
    if (isset($results[DICE_STAR])) {
      Notifications::message(clienttranslate('Bridge blown successfull'));
      //Notifications::removeTerrain($selectedBridge);
      //Terrains::remove($terrainid);
     //Terrains::DB()->delete($terrainId);

      //ou bien
      $selectedBridge->removeFromBoard();
      // remove from Database in order not to select it again
      Terrains::remove($selectedBridge);
      // TO DO priorty 2 : add broken bridge  and units on it
      // if units on it, destroy it and give asociated medal
      // gain medals if applicable 

    } else {
      Notifications::message(clienttranslate('Bridge blown unsuccessfull'));
      // TO DO remove return still Debug
      return;
    }
        
    //$card = $player->getCardInPlay();
    
    // TODO replace with $this->nextState('draw'); keep return until end of Debug
    return ;
  }

}
