<?php
namespace M44\Cards\Standard;
use M44\Managers\Units;
use M44\Board;
use M44\Core\Game;
use M44\Core\Globals;
use M44\Scenario;

class Recon extends \M44\Models\SectionCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_RECON;
    $this->name = clienttranslate('Recon');
    $this->subtitles = [
      clienttranslate('Issue an order to 1 unit on the Left Flank'),
      clienttranslate('Issue an order to 1 unit in the Center'),
      clienttranslate('Issue an order to 1 unit on the Right Flank'),
    ];
    $this->text = [
      clienttranslate('Order 1 Unit'),
      clienttranslate('When drawing a new Command card, draw two, choose one and discard the other'),
    ];
    $this->nUnits = 1;
    $this->orderUnitsTitles = [
      clienttranslate('on the Left Flank'),
      clienttranslate('in the Center'),
      clienttranslate('on the Right Flank'),
    ];

    $this->draw = ['nDraw' => 2, 'nKeep' => 1];
  }

  public function nextStateAfterPlay()
  {
    if ($this->getExtraDatas('hill317') === true) {
      return 'airpower';
    } else {
      return parent::nextStateAfterPlay();
    }
  }

  public function canHill317()
  {
    return true;
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
    if (!AirPower::areUnitsContiguous($unitIds)) {
      throw new \BgaUserException(clienttranslate('You must select a contiguous sequence of adjacent ennemy units'));
    }

    // check that one unit is in the section
    $found = false;
    $section = array_search(1, $this->getSections());
    $section = 2 - $section; // Flip it because it's computed from the ennmey point of vue
    foreach (Units::getMany($unitIds) as $unit) {
      if (in_array($section, $unit->getSections())) {
        $found = true;
      }
    }
    if ($found == false) {
      throw new \BgaUserException(clienttranslate('No ennemy in the card section.'));
    }

    // Create all the corresponding attacks
    $player = $this->getPlayer();
    $nDice = $player->getTeam()->getId() == ALLIES ? 2 : 1;
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

    Game::get()->nextState('attack');
  }

  public function getDrawMethod()
  {
    if ($this->getExtraDatas('hill317') === true) {
      return ['nDraw' => 1, 'nKeep' => 1];
    }
    return $this->draw;
  }

  public function getHits($type, $nb)
  {
    if ($this->getExtraDatas('hill317') === true) {
      $this->hitMap[DICE_STAR] = true;
    }
    if ($this->hitMap[$type]) {
      return $nb;
    }

    return -1;
  }

  public function cannotIgnoreFlags()
  {
    if ($this->getExtraDatas('hill317') === true) {
      return true;
    }

    return $this->cannotIgnoreFlags;
  }
}
