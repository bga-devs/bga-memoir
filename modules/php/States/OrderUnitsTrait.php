<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Units;

trait OrderUnitsTrait
{
  function argsOrderUnits($player = null)
  {
    $player = $player ?? Players::getActive();
    $card = $player->getCardInPlay();
    return $card->getArgsOrderUnits();
  }

  function stOrderUnits($player = null)
  {
    $player = $player ?? Players::getActive();
    $args = $this->argsOrderUnits($player);
    if ($args['n'] == \INFINITY) {
      $this->actOrderUnits($args['units']->getIds(), [], true);
    }
  }

  function actOrderUnits($unitIds, $onTheMoveIds, $auto = false)
  {
    // Sanity checks
    if (!$auto) {
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

    $selectableIds = $args['units']->getIds();
    if (count(array_diff($unitIds, $selectableIds)) != 0) {
      throw new \feException('You selected a unit that cannot be selected');
    }

    if (count(array_diff($onTheMoveIds, $selectableIds)) != 0) {
      throw new \feException('You selected a unit that cannot be selected');
    }

    // TODO : add sanity check for sections !

    // Flag the units as activated by the corresponding card
    $card = $player->getCardInPlay();
    foreach ($unitIds as $unitId) {
      Units::get($unitId)->activate($card);
    }
    foreach ($onTheMoveIds as $unitId) {
      Units::get($unitId)->activate($card, true);
    }

    // Notify
    Notifications::orderUnits($player, Units::get($unitIds), Units::get($onTheMoveIds));
    $this->gamestate->nextState('moveUnits');
  }
}
