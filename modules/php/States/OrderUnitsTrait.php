<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Troops;

trait OrderUnitsTrait
{
  function argsOrderUnits($player = null)
  {
    $player = $player ?? Players::getActive();
    $card = $player->getCardInPlay();
    return $card->getArgsOrderUnits();
  }


  function actOrderUnits($unitIds, $onTheMoveIds, $auto = false)
  {
    // Sanity checks
    if(!$auto){
      $this->checkAction('actOrderUnits');
    }
    $player = Players::getCurrent();
    $args = $this->argsOrderUnits($player);
    if (count($unitIds) > $args['n']) {
      throw new \BgaVisibleSystemException('More units than authorized. Should not happen');
    }
    if (count($onTheMoveIds) > $args['nOnTheMove']) {
      throw new \BgaVisibleSystemException('More on the move units than authorized. Should not happen');
    }

    $selectableIds = $args['troops']->getIds();
    if (count(array_diff($unitIds, $selectableIds)) != 0) {
      throw new \feException('You selected a troop that cannot be selected');
    }

    if (count(array_diff($onTheMoveIds, $selectableIds)) != 0) {
      throw new \feException('You selected a troop that cannot be selected');
    }

    // Flag the units as activated by the corresponding card
    $card = $player->getCardInPlay();
    foreach ($unitIds as $unitId) {
      Troops::get($unitId)->activate($card);
    }
    foreach ($onTheMoveIds as $unitId) {
      Troops::get($unitId)->activate($card, true);
    }

    // Notify
    //Notifications::orderUnits($player, Troop::get($unitIds), Troops::get($onTheMoveIds));

    $this->gamestate->nextState('moveUnits');
  }
}
