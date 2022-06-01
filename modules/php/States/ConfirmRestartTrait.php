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
use M44\Board;
use M44\Helpers\Log;

trait ConfirmRestartTrait
{
  public function argsConfirmTurn()
  {
    return [
        //'previousEngineChoices' => Globals::getEngineChoices(),
      ];
  }

  public function actRestart()
  {
    self::checkAction('actRestart');
    if (Log::getAll()->empty()) {
      throw new \BgaVisibleSystemException('Nothing to undo');
    }

    Log::revertAll();
    Globals::fetch();
    Board::init();

    // Refresh interface
    $datas = $this->getAllDatas(-1);
    unset($datas['prefs']);
    unset($datas['discard']);
    unset($datas['scenario']);
    unset($datas['terrains']);
    unset($datas['units']);
    unset($datas['canceledNotifIds']);
    Notifications::smallRefreshInterface($datas);
    $player = Players::getCurrent();
    Notifications::smallRefreshHand($player);

    $this->gamestate->jumpToState(Globals::getLogState());
  }

  public function stConfirmTurn()
  {
    // Check user preference to bypass if DISABLED is picked
    $pref = Players::getActive()->getPref(OPTION_CONFIRM);
    if ($pref == OPTION_CONFIRM_DISABLED) {
      $this->actConfirmTurn();
    }
  }

  public function actConfirmTurn()
  {
    self::checkAction('actConfirmTurn');
    $this->nextState('confirm');
  }
}
