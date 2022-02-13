<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Units;
use M44\Helpers\Utils;
use M44\Board;

trait MoveUnitsTrait
{
  public function argsMoveUnits($player = null)
  {
    $player = $player ?? Players::getActive();
    $card = $player->getCardInPlay();
    return $card->getArgsMoveUnits();
  }

  public function actMoveUnit($unitId, $x, $y)
  {
    // Sanity checks
    self::checkAction('actMoveUnit');
    $player = Players::getCurrent();
    $args = $this->argsMoveUnits($player);
    if (!\array_key_exists($unitId, $args['units'])) {
      throw new \BgaVisibleSystemException('You cannot move this unit. Should not happen');
    }
    $cells = $args['units'][$unitId];
    $k = Utils::array_usearch($cells, function ($cell) use ($x, $y) {
      return $cell['x'] == $x && $cell['y'] == $y;
    });
    if ($k === false) {
      throw new \BgaVisibleSystemException('You cannot move this unit to this hex. Should not happen');
    }

    if (Globals::getUnitMoved() != $unitId && Globals::getUnitMoved() != -1) {
      // force all moves of previous unit
      $prevUnit = Units::get(Globals::getUnitMoved());
      // $prevUnit->setMoves($prevUnit->getMovementRadius());
      $prevUnit->setMoveDone();
    }

    Globals::setUnitMoved($unitId);

    // Move the unit
    $cell = $cells[$k];
    $unit = Units::get($unitId);
    $unit->incMoves($cell['d']);
    $unit->moveTo($cell);
    Board::refreshUnits();
    Notifications::moveUnit($player, $unitId, $x, $y);

    // TODO : notification
    $this->gamestate->nextState('moveUnits');
  }

  public function actMoveUnitsDone()
  {
    self::checkAction('actMoveUnitsDone');
    $this->gamestate->nextState('attackUnits');
  }

  public function stMoveUnits()
  {
    $player = Players::getActive();
    $args = $this->argsMoveUnits($player);
    foreach ($args['units'] as $unitId => $cells) {
      if (!empty($cells)) {
        return;
      }
    }
    $this->gamestate->nextState('attackUnits');
  }
}
