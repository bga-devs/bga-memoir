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
use M44\Managers\Tokens;

trait DebugTrait
{
  function load($scenario)
  {
    Globals::setRound(0);
    Scenario::loadId($scenario);
    $this->stNewRound(true);
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
    // throw new \feException(print_r(Utils::revertCoords($c)));
    // $terrains = Board::getTerrainsInCell(5, 3);
    // $x = 0;
    // foreach ($terrains as $t) {
    //   throw new \feException($t->isOriginalOwner(Units::get(9)));
    //   // $x += $t->defense(Units::get(9));
    // }
    // throw
    throw new \feException('titi ' . Units::get(1086)->isCamouflaged() . ' toto');
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

  function lm()
  {
    $this->gamestate->setAllPlayersMultiactive();

    $this->gamestate->jumpToState(\ST_UPLOAD_SCENARIO);
  }
}
