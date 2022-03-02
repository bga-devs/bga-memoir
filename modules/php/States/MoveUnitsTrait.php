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
  public function argsMoveUnits($player = null, $clearPaths = false)
  {
    $player = $player ?? Players::getActive();
    $card = $player->getCardInPlay();
    $args = $card->getArgsMoveUnits();
    Utils::clearPaths($args['units'], $clearPaths);
    $args['lastUnitMoved'] = Globals::getUnitMoved();
    return $args;
  }

  public function actMoveUnit($unitId, $x, $y)
  {
    // Sanity checks
    self::checkAction('actMoveUnit');
    $player = Players::getCurrent();
    $args = $this->argsMoveUnits($player, false);
    if (!\array_key_exists($unitId, $args['units'])) {
      throw new \BgaVisibleSystemException('You cannot move this unit. Should not happen');
    }
    $cells = $args['units'][$unitId];
    $k = Utils::searchCell($cells, $x, $y);
    if ($k === false) {
      throw new \BgaVisibleSystemException('You cannot move this unit to this hex. Should not happen');
    }

    // Move the unit
    $cell = $cells[$k];
    $path = $cell['paths'][0]; // Take the first path
    $unit = Units::get($unitId);
    foreach ($path as $c) {
      Notifications::moveUnit($player, $unitId, $c['x'], $c['y']);
      Board::moveUnit($unit, $c); // TODO : maybe we need to update moves of unit along the path for some terrains ?
    }
    $unit->incMoves($cell['d']);
    Globals::setUnitMoved($unitId);
    Board::refreshUnits();

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
