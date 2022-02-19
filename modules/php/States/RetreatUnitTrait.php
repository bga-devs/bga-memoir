<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Units;
use M44\Helpers\Utils;
use M44\Board;

trait RetreatUnitTrait
{
  /**
   * Fetch retreat relevant informations
   */
  public function getRetreatInfo()
  {
    $data = Globals::getRetreat();
    return [Units::get($data['unit']), $data['min'], $data['max']];
  }

  /**
   * Compute the units that can attack
   */
  public function argsRetreatUnit()
  {
    $player = Players::getActive();
    list($unit, $minFlags, $maxFlags) = $this->getRetreatInfo();
    $cells = Board::getReachableCellsForRetreat($unit, $minFlags, $maxFlags);
    return $cells;
  }

  /**
   * Automatically resolve state if only one possible choice
   */
  public function stRetreatUnit()
  {
  }
}
