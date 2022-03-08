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
  }

  public function nextStateAfterPlay()
  {
    return 'airpower';
  }

  public function argsTargetAirPower()
  {
    $player = $this->getPlayer();
    $otherTeam = $player->getTeam()->getId() == ALLIES ? AXIS : ALLIES;
    $oUnits = Units::getOfTeam($otherTeam);
    $nDice = $otherTeam == ALLIES ? 1 : 2;
    $units = [];

    foreach ($oUnits as $oUnit) {
      $unit = [];
      $unit['x'] = $oUnit->getX();
      $unit['y'] = $oUnit->getY();
      $unit['dice'] = $nDice;
      $units[$oUnit->getId()] = $unit;
    }

    return ['units' => $units];
  }

  public function actTargetAirPower($unitIds)
  {
    $player = $this->getPlayer();
    // check that Ids are ennemy
    $args = $this->argsTargetAirPower();

    if (count(array_diff($unitIds, array_keys($args['units']))) > 0) {
      throw new \feException('Those units cannot be attacked. Should not happen');
    }

    // check adjacent of Units
    if (!Board::areAdjacent($unitIds)) {
      throw new \BgaUserException(clienttranslate('You must select adjacent ennemy units'));
    }

    if (count($unitIds) > 4) {
      throw new \BgaUserException(clienttranslate('You must maximum 4 units'));
    }

    $stack = Globals::getAttackStack();
    foreach ($unitIds as $unitId) {
      $stack[] = [
        'pId' => $player->getId(),
        'unitId' => -1,
        'x' => $args['units'][$unitId]['x'],
        'y' => $args['units'][$unitId]['y'],
        'oppUnitId' => $unitId,
        'nDice' => $args['units'][$unitId]['dice'],
        'distance' => 0,
        'ambush' => false,
      ];
    }
    Globals::setAttackStack($stack);

    // set extra data
    // $this->setExtraDatas('unitsToAttack', $unitsToAttack);
    Game::get()->nextState('attack');
  }
}
