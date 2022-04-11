<?php
namespace M44\Cards\Standard;
use M44\Core\Game;
use M44\Core\Notifications;
use M44\Managers\Units;
use M44\Dice;
use M44\Helpers\Log;

class FinestHour extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_FINEST_HOUR;
    $this->name = clienttranslate('Their Finest Hour');
    $this->text = [
      clienttranslate('Roll 1 battle die for each command card you have, including this card.'),
      clienttranslate(
        'For each unit symbol rolled, 1 unit of this type is ordered. For each star rolled, 1 unit of your choice is ordered.'
      ),
      clienttranslate('Ordered units battle with 1 additional die.'),
      clienttranslate('Reshuffle the deck and discard pile.'),
    ];
  }

  public function nextStateAfterPlay()
  {
    return 'finestHour';
  }

  public function stFinestHourRoll()
  {
    $player = $this->getPlayer();
    $n = $player->countAllCards();
    $results = Dice::roll($player, $n);
    $this->setExtraDatas('dice', $results);
    Game::get()->nextState('selectUnits');
  }

  public function getResults()
  {
    $results = $this->getExtraDatas('dice');
    return [$results[\DICE_INFANTRY] ?? 0, $results[DICE_ARMOR] ?? 0, $results[\DICE_STAR] ?? 0];
  }

  public function stOrderUnitsFinestHour()
  {
    $args = $this->argsOrderUnitsFinestHour();
    foreach ($args['units'] as $type) {
      if ($args['results'][$type - 1] > 0 || $args['results'][2] > 0) {
        return;
      }
    }
    $this->actOrderUnitsFinestHour([]);
  }

  public function argsOrderUnitsFinestHour()
  {
    $units = $this->getPlayer()
      ->getUnits()
      ->map(function ($unit) {
        return $unit->getType();
      });

    return [
      'i18n' => ['unitDesc'],
      'results' => $this->getResults(),
      'units' => $units,
      'unitDesc' => $this->computeUnitsDesc(),
    ];
  }

  public function actOrderUnitsFinestHour($unitIds)
  {
    // TODO : add sanity checks

    // Flag the units as activated by the corresponding card and notify
    $player = $this->getPlayer();
    if (!empty($unitIds)) {
      foreach ($unitIds as $unitId) {
        Units::get($unitId)->activate($this);
      }
      Notifications::orderUnits($player, Units::getMany($unitIds));
    }

    Game::get()->nextState('moveUnits');
  }

  public function getDiceModifier($unit, $cell)
  {
    return 1;
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
}
