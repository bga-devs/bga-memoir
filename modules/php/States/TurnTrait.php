<?php

namespace M44\States;

use M44\Core\Globals;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Cards;
use M44\Managers\Medals;
use M44\Managers\Units;
use M44\Core\Notifications;
use M44\Scenario;
use M44\Dice;
use M44\Helpers\Log;

trait TurnTrait
{
  function stEndTurn()
  {
    // PEGASUS
    if (Scenario::getId() == 2 && Globals::getTurn() <= 4 && Globals::getTeamTurn() == AXIS) {
      // TODO : better handling of drawing for teams
      $player = Players::getActive();
      $cards = Cards::draw(1, ['hand', $player->getId()]);
      Notifications::drawCards($player, $cards);
    }

    if (Cards::countInLocation('deck') == 0 && Globals::getDefaultWinner() != null) {
      Cards::reshuffleListener();
      return;
    }

    // Change team
    Teams::changeTeamTurn();

    // Update all tables with temp data
    Units::reset();
    Globals::setAttackStack([]);
    Globals::setUnitMoved(-1);
    Globals::setUnitAttacker(-1);
    Notifications::clearUnitsStatus();
    $this->nextState('next');
  }

  function stPrepareTurn()
  {
    // Init Hands just after Reserve deployement state in campaign mode only
    if (Globals::isCampaign() && !Globals::getInitHandDone()) {
      Cards::initHands();
      Globals::setInitHandDone(true);
    }

    Globals::incTurn();
    Medals::checkBoardMedals(true);
    if (Teams::checkVictory()) {
      return;
    }

    $team = Teams::getTeamTurn();
    $player = $team->getMembers()->first();

    $visibility = Globals::getNightVisibility();
    $scenario = Globals::getScenario();
    $team_turn = isset($scenario['game_info']['options']['night_visibility_team_turn']) ?
      $scenario['game_info']['options']['night_visibility_team_turn'] : ALLIES;
    if ($team->getId() == $team_turn && $visibility < \INFINITY) {
      $results = Dice::roll($player, 4);
      $star = $results[DICE_STAR] ?? 0;

      if ($star + $visibility > 6) {
        $star = 6 - $visibility;
      }
      $visibility += $star;

      if ($star > 0) {
        Globals::incNightVisibility($star);
        Notifications::visibility($star);
      }
      Notifications::message(clienttranslate('Night visibility is ${vis}'), ['vis' => $visibility]);

      if ($visibility >= 6) {
        Globals::setNightVisibility(\INFINITY);
      }
    }

    Log::enable();
    Log::checkpoint();
    Log::clearAll();

    // TODO : Option Airdrop2 check if airdrop2 conditions (eg option if night visibility is full)
    // if current player side is airdrop2 side
    // Check for options
    $options = Scenario::getOptions();
    $AirDrop2Done = Globals::getAirDrop2Done();

    if (isset($options['airdrop2']) && $options['airdrop2']['side'] == $team->getId() && !$AirDrop2Done) {
      if (
        isset($options['airdrop2']['option'])
        && $options['airdrop2']['option'] == 'NEED_FULL_DAY_VISIBILITY'
      ) {
        if (Globals::getNightVisibility() >= 6) {
          Globals::setAirDrop2Done(true);
          $this->nextState('airDrop2', $player->getId());
          return;
        }
      }
    }

    // TODO : Overlord => branch here to distribute cards instead
    if (true) {
      $player = $team->getMembers()->first();
      $this->giveExtraTime($player->getId());
      $transition = 'playCard';
      if ($player->isCommissar() && $player->getCommissarCard() != null) {
        $transition = 'commissar';
      }

      $this->nextState($transition, $player->getId());
    } else {
      // Activate commander in chief only
      // TODO
      $this->nextState('distributeCard');
    }
  }
}
