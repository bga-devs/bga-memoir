<?php
namespace M44\States;

use M44\Core\Globals;
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

  function actOrderUnits($units, $onTheMove)
  {
    $player = Players::getCurrent();

    $args = $this->argsOrderUnits();
    if (count($units) > $args['n']) {
      throw new \BgaVisibleSystemException('More units than authorized. Should not happen');
    }

    if (count($onTheMove) > $args['nOnTheMove']) {
      throw new \BgaVisibleSystemException('More units than authorized. Should not happen');
    }

    $troopsId = $args['troops']->getIds();

    if (count(array_diff($units, $troopsId)) != 0) {
      throw new \feException('You selected a troop that cannot be selected');
    }

    if (count(array_diff($onTheMove, $troopsId)) != 0) {
      throw new \feException('You selected a troop that cannot be selected');
    }

    $card = $player->getCardInPlay();

    foreach ($units as $unit) {
      $troop = Troops::get($unit);
      $troop->setActivationCard($card->getId());
    }

    foreach ($onTheMove as $unitO) {
      $troop = Troops::get($unitO);
      $troop->setActivationCard($card->getId());
      $troop->setExtraDatas('onTheMove', true);
    }

    $this->gamestate->nextState('moveUnits');
  }
}
