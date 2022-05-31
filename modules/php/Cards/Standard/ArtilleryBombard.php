<?php
namespace M44\Cards\Standard;

use M44\Managers\Units;

class ArtilleryBombard extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_ARTILLERY_BOMBARD;
    $this->name = clienttranslate('Artillery Bombard');
    $this->text = [
      clienttranslate('Issue an order to all <ARTILLERY> units.'),
      clienttranslate('Units may move up to 3 hexes or battle twice.'),
      clienttranslate('If you do not command any artillery units, issue an order to 1 unit of your choice.'),
    ];
  }

  public function getArgsOrderUnits()
  {
    $player = $this->getPlayer();
    $marineCommand = $player->isMarineCommand();
    $units = $player->getUnits();

    // Keep only armor
    $artillery = $units->filter(function ($unit) {
      return $unit->getType() == \ARTILLERY;
    });

    if ($artillery->empty()) {
      // No armor => 1 unit of your choice
      return [
        'i18n' => ['desc'],
        'n' => $marineCommand ? 2 : 1,
        'nTitle' => $marineCommand ? 2 : 1,
        'desc' => \clienttranslate('(because no artillery units)'),
        'units' => $units->getPositions(),
      ];
    } else {
      return [
        'i18n' => ['desc'],
        'n' => \INFINITY,
        'nTitle' => \INFINITY,
        'desc' => \clienttranslate('(artillery units only)'),
        'units' => $artillery->getPositions(),
      ];
    }
  }

  public function getArgsAttackUnits($overrideNbFights = null)
  {
    return parent::getArgsAttackUnits([\ARTILLERY => 2]);
  }
}
