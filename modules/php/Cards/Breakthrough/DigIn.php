<?php
namespace M44\Cards\Breakthrough;
use M44\Managers\Units;

class DigIn extends \M44\Cards\Standard\DigIn
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->text = [
      clienttranslate('Issue an order to 4 <INFANTRY> and/or <ARTILLERY> units.'),
      clienttranslate('The units improve their position by placing an available sandbag on the units\' hexes.'),
      clienttranslate(
        'If you do not command any infantry or artillery units, issue an order to 1 unit of your choice.'
      ),
    ];
  }

  public function getArgsOrderUnits()
  {
    $player = $this->getPlayer();
    $marineCommand = $player->isMarineCommand();
    $units = $player->getUnits();

    // Keep only infantry and artillery
    $validUnits = $units->filter(function ($unit) {
      return in_array($unit->getType(), [INFANTRY, ARTILLERY]);
    });

    if ($validUnits->empty()) {
      // No infantry => 1 unit of your choice
      return [
        'i18n' => ['desc'],
        'n' => $marineCommand ? 2 : 1,
        'nTitle' => $marineCommand ? 2 : 1,
        'desc' => \clienttranslate('(because no infantry nor artillery units)'),
        'units' => $units,
      ];
    } else {
      return [
        'i18n' => ['desc'],
        'n' => $marineCommand ? 5 : 4,
        'nTitle' => $marineCommand ? 5 : 4,
        'desc' => \clienttranslate('(for improving positions)'),
        'units' => $validUnits,
      ];
    }
  }

  public function nextStateAfterOrder($unitIds, $onTheMoveIds)
  {
    if (count($unitIds) == 1 && !in_array(Units::get($unitIds)->getType(), [\INFANTRY, ARTILLERY])) {
      return 'moveUnits';
    } else {
      return 'digIn';
    }
  }
}
