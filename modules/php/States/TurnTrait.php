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
      $cards = Cards::pickForLocation(1, 'deck', ['hand', $player->getId()]);
      Notifications::drawCards($player, $cards);
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
    Globals::incTurn();
    Medals::checkBoardMedals(true);
    if (Teams::checkVictory()) {
      return;
    }

    $team = Teams::getTeamTurn();
    $player = $team->getMembers()->first();
    if ($team->getId() == ALLIES && Globals::getNightVisibility() < \INFINITY) {
      $results = Dice::roll($player, 4);
      $star = $results[DICE_STAR] ?? 0;

      if ($star > 0) {
        Globals::incNightVisibility($star);
        Notifications::visibility($star);
      }
      Notifications::message(clienttranslate('Night visibility is ${vis}'), ['vis' => Globals::getNightVisibility()]);

      if (Globals::getNightVisibility() >= 6) {
        Globals::setNightVisibility(\INFINITY);
      }
    }

    Log::enable();
    Log::checkpoint();
    Log::clearAll();

    // TODO : Overlord => branch here to distribute cards instead
    if (true) {
      $player = $team->getMembers()->first();
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
