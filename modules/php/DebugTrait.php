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
    // throw new \feException('titi ' . Units::get(1086)->isCamouflaged() . ' toto');
    // $this->argsAttackUnit();
    $before = memory_get_usage();
    $toto = Scenario::getMetadataFromTheFront('STANDARD', [], false);
    // throw new \feException(count($toto));
    $after = memory_get_usage();
    // throw new \feException(print_r(reset($toto)));
    throw new \feException($after - $before);
  }

  function val($scenario)
  {
    $scenarios = [];
    require_once dirname(__FILE__) . '/FromTheFront/' . $scenario . '.php';
    $scenarId = (int) explode('-', $scenario)[0];
    $scenario = $scenarios[$scenarId];
    uc($scenario['game_info']['side_player1']);
    uc($scenario['game_info']['side_player2']);
    uc($scenario['board']['type']);
    uc($scenario['board']['face']);

    if (isset($scenario['board']['hexagons']['item'])) {
      $scenario['board']['hexagons'] = $scenario['board']['hexagons']['item'];
    }
    $valid = Scenario::validateScenario($scenario);
    if ($valid === true) {
      throw new \feException('Valid');
    } else {
      throw new \feException('KO');
    }
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

  function move($id, $x, $y)
  {
    $unit = Units::get($id);
    $unit->moveTo(['x' => $x, 'y' => $y]);
    Notifications::moveUnit(Players::getActive(), $unit, ['x' => $x, 'y' => $y], ['x' => $x, 'y' => $y]);
  }
}
