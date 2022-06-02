<?php
namespace M44\Cards\Standard;

use M44\Managers\Units;
use M44\Board;
use M44\Core\Game;
use M44\Core\Globals;

class AirPower extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = CARD_AIR_POWER;
    $this->name = clienttranslate('Air power');
    $this->text = [
      clienttranslate('Target a group of 4 or fewer enemy units adjacent to each other.'),
      clienttranslate(
        'Roll 2 battle dice per hex (Allied air attack) or 1 per hex (Axis air attack), ignoring any terrain battle die reduction.'
      ),
      clienttranslate("Score 1 hit for each die that matches the unit's symbol, a grenade or a star."),
      clienttranslate('For each flag, retreat 1 hex.'),
      clienttranslate('Flags may not be ignored.'),
    ];
    $this->cannotIgnoreFlags = true;
    $this->hitMap[DICE_STAR] = true;
    $this->checkSide = true; // Useful for breakthrough
  }

  public function nextStateAfterPlay()
  {
    if (Globals::getNightVisibility() != \INFINITY) {
      return parent::nextStateAfterPlay();
    }
    return 'airpower';
  }

  public function cannotIgnoreFlags()
  {
    if (Globals::getNightVisibility() != \INFINITY) {
      return false;
    }
    return true;
  }

  public function getHits($type, $nb)
  {
    if (Globals::getNightVisibility() != \INFINITY) {
      return -1;
    }
    return parent::getHits($type, $nb);
  }

  public function getArgsOrderUnits()
  {
    $player = $this->getPlayer();
    $marineCommand = $player->isMarineCommand();
    $units = $player->getUnits()->getPositions();
    return [
      'n' => $marineCommand ? 2 : 1,
      'nTitle' => $marineCommand ? 2 : 1,
      'nOnTheMove' => 0,
      'desc' => '',
      'sections' => [\INFINITY, \INFINITY, INFINITY],
      'units' => $units,
    ];
  }

  public function argsTargetAirPower()
  {
    $units = $this->getPlayer()
      ->getTeam()
      ->getOpponent()
      ->getUnits()
      ->map(function ($unit) {
        return $unit->getPos();
      });
    return ['units' => $units];
  }

  public function actTargetAirPower($unitIds)
  {
    // Sanity checks
    $args = $this->argsTargetAirPower();
    if (count(array_diff($unitIds, $args['units']->getIds())) > 0) {
      throw new \feException('Those units cannot be attacked. Should not happen');
    }
    if (count($unitIds) > 4) {
      throw new \BgaUserException(clienttranslate('You must choose maximum 4 units'));
    }
    // check adjacent of Units
    if (!$this->areUnitsContiguous($unitIds)) {
      throw new \BgaUserException(clienttranslate('You must select a contiguous sequence of adjacent enemy units'));
    }

    // Create all the corresponding attacks
    $player = $this->getPlayer();
    $nDice = !$this->checkSide || $player->getTeam()->getId() == ALLIES ? 2 : 1;
    $stack = Globals::getAttackStack();
    foreach (array_reverse($unitIds) as $unitId) {
      $stack[] = [
        'pId' => $player->getId(),
        'unitId' => -1,
        'cardId' => $this->getId(),
        'x' => $args['units'][$unitId]['x'],
        'y' => $args['units'][$unitId]['y'],
        'oppUnitId' => $unitId,
        'nDice' => $nDice,
        'distance' => 0,
        'ambush' => false,
      ];
    }
    Globals::setAttackStack($stack);

    // set extra data
    // $this->setExtraDatas('unitsToAttack', $unitsToAttack);
    Game::get()->nextState('attack');
  }

  public static function areUnitsContiguous($unitIds)
  {
    $previousUnit = null;
    foreach ($unitIds as $unitId) {
      $unit = Units::get($unitId);
      if (!is_null($previousUnit)) {
        $pos1 = $unit->getPos();
        $pos2 = $previousUnit->getPos();
        if (abs($pos1['x'] - $pos2['x']) + 2 * abs($pos1['y'] - $pos2['y']) > 3) {
          return false;
        }
      }
      $previousUnit = $unit;
    }

    return true;
  }
}
