<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Core\Stats;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Cards;
use M44\Managers\Units;
use M44\Core\Notifications;
use M44\Scenario;

trait RoundTrait
{
  public function stNewRound()
  {
    $round = Globals::incRound();
    $maxRound = Globals::isTwoWaysGame() ? 2 : 1;
    if ($round > $maxRound) {
      $this->gamestate->jumpToState(\ST_END_OF_GAME);
      return;
    }

    $rematch = $round == 2;
    Scenario::setup($rematch);
    Globals::setUnitMoved(-1);
    Globals::setUnitAttacker(-1);
    Globals::setLastPlayedCards([]);
    Globals::setAttackStack([]);

    // Check for options
    $options = Scenario::getOptions();
    if(isset($options['airdrop'])){
      $team = Teams::get($options['airdrop']['side']);
      $this->nextState('airdrop', $team->getCommander());
      return;
    }

    $this->gamestate->jumpToState(\ST_PREPARE_TURN);
  }

  public function stEndOfGame()
  {
    $nRounds = Globals::isTwoWaysGame()? 2 : 1;

    foreach (Players::getAll() as $player) {
      $nWins = 0;
      $nFigs = 0;
      for($i = 1; $i <= $nRounds; $i++){
        $nWins += $player->getStat('statusRound' . $i);
        foreach(['inf', 'armor', 'artillery'] as $type){
          $nFigs += $player->getStat($type . 'FigRound' . $i);
        }
      }

      $player->setScore($nWins);
      $player->setScoreAux($nFigs);
    }

    $this->gamestate->nextState('');
  }
}
