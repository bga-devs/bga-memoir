<?php
namespace M44;
use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Core\Preferences;
use M44\Helpers\Utils;
use M44\Managers\Cards;
use M44\Managers\Units;
use M44\Managers\Players;
use M44\Board;

trait DebugTrait
{
  function load($scenario)
  {
    Globals::setRound(1);
    Scenario::load($scenario);
    $this->gamestate->jumpToState(\ST_NEW_ROUND);
  }

  function vt($c)
  {
    // $this->actChooseCard([35]);
    // throw new \feException(print_r(Cards::getInPlayOfAll()));
    // $this->actTargetAirPower([5, 4]);
    // foreach (Board::getTerrainsInCell(['x' => 5, 'y' => 3]) as $terrain) {
    //   if ($terrain->isBunker(['x' => 5, 'y' => 3])) {
    //     $terrain->setExtraDatas('zut', 'mahcin');
    //     $terrain->setExtraDatas('truc', 'mm');
    //   }
    // }
    // throw new \feException(print_r(Board::getReachableCellsAtDistance(Units::get(14), 3)));
    throw new \feException(print_r(Utils::revertCoords($c)));
  }

  function tp($pId, $unitId, $min, $max)
  {
    Globals::setRetreat([
      'unit' => $unitId,
      'min' => $min,
      'max' => $max,
    ]);
    $this->changeActivePlayerAndJumpTo((int) $pId, \ST_RETREAT);
  }

  function test()
  {
    $hillComponents = Board::getHillComponents();
    var_dump($hillComponents);
  }
}
