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
use M44\Managers\Medals;
use M44\Managers\Terrains;
use M44\Core\Stats;
use M44\Models\Player;

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
    if (Globals::isCampaign() && Globals::getAirPowerTokenUsed()) {
      $card = Cards::getInstance(CARD_AIR_POWER);
      $card->setPlayer($player->getId());
    } else {
      $card = $player->getCardInPlay();
    }
    $args = $card->argsTargetAirPower();
    $args['actionCount'] = Globals::getActionCount();
    return $args;
  }

  public function actTargetAirPower($unitIds)
  {
    self::checkAction('actTargetAirPower');
    Globals::incActionCount();
    $player = Players::getActive();
    if (Globals::isCampaign() && Globals::getAirPowerTokenUsed()) {
      $card = Cards::getInstance(CARD_AIR_POWER);
      $card->setPlayer($player->getId());
      $card->setId(41); 
    } else {
      $card = $player->getCardInPlay();
    }
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
    return $card->blowBridgeFilters($card);
  }

  public static function isTerrainInCardSections($terrain, $cardsections, $side)
  {
    $isInSection = false;
    foreach ($terrain->getSections($side) as $s) {
      if (!$isInSection) {
        $isInSection = ($cardsections[$s] != 0);
      }
    }
    return $isInSection;
  }

  public function actBlowBridge($terrainId)
  {
    self::checkAction('actBlowBridge');
    Globals::incActionCount();

    $args = $this->argsblowbridge();

    $selectedBridge = Terrains::get($terrainId);
    $player = Players::getCurrent();
    $teamId = $player->getTeam()->getId();

    // Roll 2 dices, on star, destroy bridge
    $bonusmedal = Board::oneMedalIfBlownCell($selectedBridge->getPos());
    $results = Dice::roll($player, 2);
    if (isset($results[DICE_STAR])) {
      Notifications::message(clienttranslate('Bridge blown successfull'));
      $selectedBridge->removeFromBoard();
      // remove bridge from Database in order not to select it again
      Terrains::remove($selectedBridge);

      // add broken bridge tile
      $brokenbridge = Terrains::add([
        'type' => 'brokenbridge',
        'tile' => 'brkbridge',
        'x' => $selectedBridge->getPos()['x'],
        'y' => $selectedBridge->getPos()['y'],
        'orientation' => $selectedBridge->getOrientation(),
      ]);

      Notifications::addTerrain(
        $player,
        $brokenbridge,
        ''
      );

      // if units on it, destroy it and give asociated medal
      // get pos of selected bridge 
      $bridge_cell = $selectedBridge->getPos();

      // check if unit on this cell, if so eliminate it and add related medals
      $unitOnBridge = Board::getUnitInCell($bridge_cell['x'], $bridge_cell['y']);

      if (isset($unitOnBridge)) {
        $playerOppUnit_tmp = Players::getOfTeam($unitOnBridge->getPlayer()->getTeam()->getOpponent()->getId());
        $playerOppUnit_tmp2 = $playerOppUnit_tmp->toArray();
        $playerOppUnit = array_shift($playerOppUnit_tmp2);
        $eliminated = $this->damageUnit($unitOnBridge, $playerOppUnit, 4);
        if (Teams::checkVictory()) {
          return;
        }
      }

      // gain medals related to bridge blown objectives scenario 'behavior2' == 'ONE_MEDAL_IF_BLOWN' if applicable
      if ($bonusmedal) {
        // Increase stats
        $statName = 'incMedalRound' . Globals::getRound();
        foreach ($player->getTeam()->getMembers() as $p) {
          Stats::$statName($p, 1);
        }

        // Create medals and notify them (will refresh previous stats)
        $medals = Medals::addDestroyedTerrainMedals($teamId, 1);
        Notifications::scoreMedals($teamId, $medals);

        if (Teams::checkVictory()) {
          return;
        }
      }
    } else {
      Notifications::message(clienttranslate('Bridge blown unsuccessfull'));
    }

    $this->nextState('draw');
  }
}
