<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Units;
use M44\Helpers\Utils;
use M44\Board;

trait AttackUnitsTrait
{
  public function argsAttackUnit($player = null)
  {
    $player = $player ?? Players::getActive();
    $card = $player->getCardInPlay();
    return $card->getArgsAttackUnits();
  }

  public function actAttackUnitsDone()
  {
    self::checkAction('actAttackUnitsDone');
    $this->gamestate->nextState('attackUnits'); // TODO
  }

  public function stAttackUnits()
  {
    $args = $this->argsAttackUnit();
    $c = 0;
    foreach ($args['units'] as $targets) {
      $c += count($targets);
    }
    if ($c == 0) {
      // No unit can be targetted, end of turn
      $this->gamestate->nextState('draw');
    }
  }

  public function actAttackUnit($unitId, $x, $y)
  {
    // Sanity checks
    self::checkAction('actAttackUnit');
    $player = Players::getCurrent();
    $args = $this->argsAttackUnit($player);
    if (!\array_key_exists($unitId, $args['units'])) {
      throw new \BgaVisibleSystemException('You cannot attack with this unit. Should not happen');
    }
    $cells = $args['units'][$unitId];
    $k = Utils::array_usearch($cells, function ($cell) use ($x, $y) {
      return $cell['x'] == $x && $cell['y'] == $y;
    });
    if ($k === false) {
      throw new \BgaVisibleSystemException('You cannot attack this hex with this unit. Should not happen');
    }

    // TODO
    die('test');
  }
}
