<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Cards;
use M44\Managers\Units;
use M44\Helpers\Utils;
use M44\Board;
use M44\Scenario;

trait AirDropTrait
{
  public function argsAirDrop()
  {
    $options = Scenario::getOptions()['airdrop'];
    $cells = Board::getListOfCells();

    return [
      'nb' => $options['nbr_units'],
      'cells' => $cells,
      'actionCount' => Globals::getActionCount(),
    ];
  }

  public function actAirDrop($x, $y)
  {
    // Sanity checks
    self::checkAction('actAirDrop');
    Globals::incActionCount();
    $args = $this->argsAirDrop();
    $k = Utils::searchCell($args['cells'], $x, $y);
    if ($k === false) {
      throw new \BgaVisibleSystemException('You cannot air drop here. Should not happen');
    }

    // Simulate random walks
    $player = Players::getCurrent();
    $options = Scenario::getOptions()['airdrop'];
    for ($i = 0; $i < $args['nb']; $i++) {
      $pos = Board::randomWalk(['x' => $x, 'y' => $y], $options['range']);
      $unit = Units::addInCell($options['unit'], $pos);

      if (is_null($pos) || Board::isImpassable($unit, $pos) || Board::getUnitInCell($pos) !== null) {
        // Unsafed landing => remove unit
        Units::remove($unit->getId());
      } else {
        // Notify about it
        Board::addUnit($unit);
        Notifications::airDrop($player, $unit);
      }
    }

    $this->gamestate->jumpToState(ST_PREPARE_TURN);
  }
}
