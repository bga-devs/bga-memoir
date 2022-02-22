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
  /**
   * Attack phase is over, go to 'draw' phase
   */
  public function actAttackUnitsDone()
  {
    self::checkAction('actAttackUnitsDone');
    $this->gamestate->nextState('draw');
  }

  /**
   * Compute the units that can attack
   */
  public function argsAttackUnit($player = null)
  {
    $player = $player ?? Players::getActive();
    $card = $player->getCardInPlay();
    $args = $card->getArgsAttackUnits();
    Utils::clearPaths($args['units']);
    if ($this->gamestate->state()['name'] == 'armorOverrunAttack') {
      $currentAttack = Globals::getCurrentAttack();
      return ['units' => [$currentAttack['unitId'] => $args['units'][$currentAttack['unitId']] ?? []]];
    }
    return $args;
  }

  /**
   * Automatically skip state if no more unit can attack
   */
  public function stAttackUnits()
  {
    throw new \feException(print_r(\debug_print_backtrace()));
    $args = $this->argsAttackUnit();
    $nTargets = 0;
    foreach ($args['units'] as $targets) {
      $nTargets += count($targets);
    }
    if ($nTargets == 0) {
      $this->actAttackUnitsDone();
    }
  }

  /**
   * Active player selected a unit and a cell he wants to attack with this unit
   */
  public function actAttackUnit($unitId, $x, $y)
  {
    // Sanity checks
    self::checkAction('actAttackUnit');

    $player = Players::getCurrent();
    $card = $player->getCardInPlay();
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
    $target = $cells[$k];
    $oppUnit = Board::getUnitInCell($x, $y);
    if ($oppUnit === null) {
      throw new \BgaVisibleSystemException('No opponent unit in this cell. Should not happen');
    }

    // Prepare attack
    $unit = Units::get($unitId);
    if ($this->gamestate->state()['name'] == 'attackUnits') {
      $unit->incFights(1);
    }
    $nDice = $card->updateDiceRoll($target['dice']);

    // log attack information
    Globals::setCurrentAttack([
      'unitId' => $unitId,
      'x' => $x,
      'y' => $y,
      'oppUnit' => $oppUnit->getId(),
      'nDice' => $nDice,
      'distance' => $target['d'],
      'retreat' => 0,
      'ambush' => false,
    ]);

    // opponent players
    // if (in_array($oppUnit->getNation(), Units::$nations[AXIS])) {
    //   $oppPlayer = Players::getSide(AXIS);
    // } else {
    //   $oppPlayer = Players::getSide(\ALLIES);
    // }

    $this->nextState('ambush', $oppUnit->getPlayer());
    return;
    // // if distance = 1, then ask for ambush
    // if ($target['d'] == 1) {
    //   $this->gamestate->nextState('ambush');
    // } else {
    //   $this->actResolveAttack($unit, $oppUnit, $nDice, $target['d']);
    // }
  }

  /**
   * Resolve an attack
   */

  public function stAttackThrow()
  {
    $player = Players::getActive();
    $currentAttack = Globals::getCurrentAttack();
    $oppUnit = Units::get($currentAttack['oppUnit']);
    $currentUnit = Units::get($currentAttack['unitId']);

    // check if initial distance = 1 & new distance != then cannot attack
    if ($currentAttack['distance'] == 1 && $currentAttack['ambush']) {
      // check new distance
      $argsAttack = $this->argsAttackUnit();
      $k = Utils::array_usearch($argsAttack['units'][$currentAttack['unitId']], function ($cell) use ($currentAttack) {
        return $cell['x'] == $currentAttack['x'] && $cell['y'] == $currentAttack['y'];
      });

      if ($k === false) {
        Notifications::message(
          clienttranslate('${player_name} unit has retreated due to Ambush. Attack cannot take place'),
          ['player' => $player]
        );
        $this->nextState('nextAttack');
        return;
      }
    }

    // launch dice
    $results = array_count_values($this->rollDice($player, $currentAttack['nDice'], $oppUnit->getPos()));
    $hits = $oppUnit->getHits($results);
    $eliminated = false;

    if ($hits > 0) {
      $eliminated = $this->damageUnit($oppUnit, $hits);
      // $eliminated = $oppUnit->takeDamage($hits);
      // Notifications::takeDamage($player, $oppUnit, $hits);
      // if ($eliminated) {
      //   //TODO : Manage scenario specific
      //   // TODO : store type of unit
      //   Teams::incMedals(1, $player->getSide());
      //   Notifications::scoreMedal($player, 1);
      // }
    }

    if (isset($results[DICE_FLAG]) && !$eliminated) {
      $currentAttack['retreat'] = $results[\DICE_FLAG];
      Globals::setCurrentAttack($currentAttack);
    }

    // TODO: manage specific cards (behind ennemy lines...)

    $this->nextState('retreat', $oppUnit->getPlayer());
    return;
    // $this->nextState('nextAttack');
  }

  public function damageUnit($unit, $hits)
  {
    $eliminated = $unit->takeDamage($hits);
    $player = $unit->getPlayer();
    Notifications::takeDamage($player, $unit, $hits);
    if ($eliminated) {
      //TODO : Manage scenario specific
      // TODO : store type of unit
      Teams::incMedals(1, Players::get(Globals::getActivePlayer())->getTeam());
      Notifications::scoreMedal(Players::get(Globals::getActivePlayer()), 1);
    }
    return $eliminated;
  }

  public function rollDice($player, $nDice, $cell = null)
  {
    $dice = [\DICE_INFANTRY, \DICE_INFANTRY, \DICE_ARMOR, \DICE_FLAG, \DICE_STAR, \DICE_GRENADE];
    $results = [];
    for ($i = 0; $i < $nDice; $i++) {
      $k = array_rand($dice);
      $results[] = $dice[$k];
    }

    // debug
    $results = [DICE_INFANTRY, DICE_INFANTRY, DICE_FLAG, DICE_FLAG];

    Notifications::rollDice($player, $nDice, $results, $cell);
    return $results;
  }
}
