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
    $data = Globals::getCurrentAttack();
    return [Units::get($data['oppUnit']), $data['retreat'], $data['retreat']];
  }

  /**
   * Compute the units that can attack
   */
  public function argsRetreatUnit()
  {
    $player = Players::getActive();
    list($unit, $minFlags, $maxFlags) = $this->getRetreatInfo();
    $cells = Board::getReachableCellsForRetreat($unit, $minFlags, $maxFlags);
    return ['cells' => $cells, 'unit' => $unit->getId()];
  }

  /**
   * Automatically resolve state if only one possible choice
   */
  public function stRetreatUnit()
  {
    $args = $this->argsRetreatUnit();
    $currentAttack = Globals::getCurrentAttack();
    $max = 0;
    foreach ($args['cells'] as $cell) {
      if ($cell['d'] > $max) {
        $max = $cell['d'];
      }
    }
    // TODO: manage cards & nations

    // if possibilities is less than flagNumber, take damage
    if ($currentAttack['retreat'] > $max) {
      $damage = $currentAttack['retreat'] - $max;
      $unit = Units::get($currentAttack['oppUnit']);
      $this->damageUnit($unit, $damage);
      $currentAttack['retreat'] -= $damage;
      Globals::setCurrentAttack($currentAttack);

      if ($damage == $currentAttack['retreat']) {
        $currentAttack['retreat'] = 0;
        Globals::setCurrentAttack($currentAttack);
        $this->nextState('armorOverrun', Globals::getActivePlayer());
        return;
      }
    }

    if ($max == 0) {
      $this->nextState('armorOverrun', Globals::getActivePlayer());
    }
  }

  public function actRetreat($x, $y)
  {
    // Sanity checks
    self::checkAction('actRetreat');
    $args = $this->argsRetreatUnit();
    $player = Players::getCurrent();
    $currentAttack = Globals::getCurrentAttack();
    $cells = $args['cells'];

    $k = Utils::array_usearch($cells, function ($cell) use ($x, $y) {
      return $cell['paths'][0][0]['x'] == $x && $cell['paths'][0][0]['y'] == $y;
    });

    if ($k === false) {
      throw new \BgaVisibleSystemException('You cannot move this unit to this hex. Should not happen');
    }

    // Move the unit
    $cell = $cells[$k];
    $path = $cell['paths'][0]; // Take the first path
    $unit = Units::get($currentAttack['oppUnit']);
    $unit->moveTo($path[0]);
    Notifications::moveUnit($player, $currentAttack['oppUnit'], $path[0]['x'], $path[0]['y']);

    // TODO listen here for mine and frozen river
    $currentAttack['retreat']--;
    Globals::setCurrentAttack($currentAttack);
    Board::refreshUnits();

    $this->nextState('retreat');
  }

  public function actRetreatDone()
  {
    // Sanity checks
    self::checkAction('actRetreatDone');
    // check that retreat = 0
    if (Globals::getCurrentAttack()['retreat'] != 0) {
      throw new \BgaUserException(clienttranslate('You did not retreat far enough'));
    }
    $this->nextState('armorOverrun', Globals::getActivePlayer());
  }
}
