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
use M44\Managers\Terrains;

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

    // Same as PEGASUS for reduce card victory roll event
    if(Globals::isCampaign()) {
      $player = Players::getActive();
      $teamId = $player -> getTeam() -> getId();
      $scenario = Scenario::get();
      $info = $scenario['game_info'];
      $mustDraw_player = $teamId == $info['side_player1'] ? 'mustDraw_player1' : 'mustDraw_player2';
      if (isset($info[$mustDraw_player]) && $info[$mustDraw_player] > 0) {
        $cards = Cards::draw(1, ['hand', $player->getId()]);
        Notifications::drawCards($player, $cards);
        $scenario['game_info'][$mustDraw_player] -= 1;
        Globals::setScenario($scenario);  
      }
    }

    if (Cards::countInLocation('deck') == 0 && Globals::getDefaultWinner() != null) {
      Cards::reshuffleListener();
      if(Teams::checkVictory()) {
        $player = Players::getActive();
        $this->nextState('endRound', $player);
        return;
      }

      return;
    }

    // Smoke screen : flip to smoke1 tile at end of first turn and remove at end of turn 3 (second turn of 1st player)
    if (Globals::getTurn() == 1) {
      $player = Players::getActive();
      // flip each smoke screen to 'smoke1' sunny tile
      $smokeScreens = Terrains::getSelectQuery()
      ->where('type', 'smokescreen')
      ->get();
      foreach ($smokeScreens as $smoke) {
        $smoke -> setTile('smoke1');
        Notifications::flipSmokeScreenMarker($player, $smoke);
      }
    }
    // End of second turn of 1st player
    if (Globals::getTurn() == 3) {
      $smokeScreens = Terrains::getSelectQuery()
      ->where('type', 'smokescreen')
      ->get();
      foreach ($smokeScreens as $smoke) {
        $smoke -> removeFromBoard();
      }

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
    Notifications::increaseTurn(Globals::getTurn());
    Medals::checkBoardMedals(true);

    $team = Teams::getTeamTurn();
    $player = $team->getMembers()->first();
    if (Teams::checkVictory()) {
      $this->nextState('endRound', $player);
      return;
    }

    $visibility = Globals::getNightVisibility();
    $scenario = Globals::getScenario();
    $team_turn = isset($scenario['game_info']['options']['night_visibility_team_turn']) ?
      $scenario['game_info']['options']['night_visibility_team_turn'] : ALLIES;
    $nightReserveRule = $scenario['game_info']['options']['night_visibility_reverse_rule'] ?? false;
    if ($nightReserveRule) {
      if ($team->getId() == $team_turn && $visibility > 1) {
        $results = Dice::roll($player, 4);
        $star = $results[DICE_STAR] ?? 0;

        /*if ($star > 0 && $visibility == \INFINITY) {
          $visibility = 6;
        }*/
  
        if ($visibility - $star < 1) {
          $star = $visibility - 1;
        }
        $visibility -= $star;
  
        if ($star > 0) {
          Globals::incNightVisibility(-$star);
          Notifications::visibility(-$star);
        }
        Notifications::message(clienttranslate('Night visibility is ${vis}'), ['vis' => $visibility]);
  
      }
    } else { // normal night rule case
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
    if (!Globals::isOverlord()) {
      $player = $team->getMembers()->first();
      $this->giveExtraTime($player->getId());
      $transition = 'playCard';
      if ($player->isCommissar() && $player->getCommissarCard() != null) {
        $transition = 'commissar';
      }

      $this->nextState($transition, $player->getId());
    } else {
      // Activate commander in chief only
      //var_dump('OVERLORD DISTRIBUTION CASE');
      $this->nextState('distributeCards');
    }
  }
}
