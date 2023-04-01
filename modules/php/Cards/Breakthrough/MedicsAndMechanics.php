<?php
namespace M44\Cards\Breakthrough;

use M44\Managers\Units;
use M44\Board;
use M44\Dice;
use M44\Core\Game;
use M44\Core\Globals;
use M44\Core\Notifications;

class MedicsAndMechanics extends \M44\Cards\Standard\MedicsAndMechanics
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->text = [
      clienttranslate('Roll 4 battle dice.'),
      clienttranslate(
        'For each die matching a unit\'s symbol, you may return 1 lost figure to duty with that unit. For each star, you may return 1 lost figure to duty with a unit of your choice'
      ),
      clienttranslate(' unit may not gain more figures than it originally had.'),
      clienttranslate('If the unit recovers at least 1 figure, it may also be issued an order.'),
    ];

    // TODO
  }

  public function nextStateAfterPlay()
  {
    return 'medicsBT';
  }

  public function stMedicsBTRoll()
  {
    $player = $this->getPlayer();
    $results = Dice::roll($player, 4);
    $this->setExtraDatas('dice', $results);
    Game::get()->nextState('healUnits');
  }

  public function argsMedicsBTHeal()
  {
    $player = $this->getPlayer();
    $units = $player->getUnits()->filter(function ($unit) {
      return $unit->isWounded() && !$unit->cannotHeal();
    });

    return [
      'i18n' => ['unitDesc'],
      'unitDesc' => $this->computeUnitsDesc(),
      'sections' => [\INFINITY, \INFINITY, \INFINITY],
      'units' => $units->map(function ($unit) {
        return $unit->getType();
      }),
      'wounds' => $units->map(function ($unit) {
        return $unit->getWounds();
      }),
      'results' => $this->getResults(),
    ];
  }

  public function getResults()
  {
    $results = $this->getExtraDatas('dice');
    return [$results[\DICE_INFANTRY] ?? 0, $results[DICE_ARMOR] ?? 0, $results[\DICE_STAR] ?? 0];
  }

  public function computeUnitsDesc()
  {
    $results = $this->getResults();
    $descs = [
      clienttranslate('${n} infantry unit(s)'),
      clienttranslate('${n} armor unit(s)'),
      clienttranslate('${n} unit(s) of your choice'),
    ];
    $desc = ['log' => [], 'args' => ['i18n' => []]];
    foreach ($results as $i => $n) {
      if ($n > 0) {
        $desc['log'][] = '${unit_' . $i . '}';
        $desc['args']['i18n'][] = 'inf';
        $desc['args']['unit_' . $i] = [
          'log' => $descs[$i],
          'args' => ['n' => $n],
        ];
      }
    }

    $desc['log'] = join(', ', $desc['log']);
    return $desc;
  }

  public function actMedicsBTHeal($unitIds)
  {
    if (empty($unitIds)) {
      Game::get()->nextState('draw');
      return;
    }

    $player = $this->getPlayer();
    $args = $this->argsMedicsBTHeal();
    foreach ($unitIds as $unitId) {
      if (!in_array($unitId, $args['units']->getIds())) {
        throw new \feException('This unit cannot be healed. Should not happen');
      }
    }

    $heals = [];
    foreach ($unitIds as $unitId) {
      $heals[$unitId] = ($heals[$unitId] ?? 0) + 1;
    }
    foreach ($heals as $unitId => $n) {
      if ($n > $args['wounds'][$unitId]) {
        throw new \feException('This unit cannot be healed that much. Should not happen');
      }
    }

    foreach ($heals as $unitId => $nHeal) {
      // Heal and then order the unit
      $unit = Units::get($unitId);
      $nHealed = $unit->heal($nHeal);
      Notifications::healUnit($player, $nHealed, $unit);
      $unit->activate($this);
      Notifications::orderUnits($player, Units::getMany([$unitId]), null);
    }
    Game::get()->nextState('move');
  }
}
