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
    $units = $player->getUnits();

    // Keep only armor
    $artillery = $units->filter(function ($unit) {
      return $unit->getType() == \ARTILLERY;
    });

    if ($artillery->empty()) {
      // No armor => 1 unit of your choice
      return [
        'i18n' => ['desc'],
        'n' => 1,
        'nTitle' => 1,
        'desc' => \clienttranslate('(because no armor units)'),
        'units' => $units,
      ];
    } else {
      return [
        'i18n' => ['desc'],
        'n' => \INFINITY,
        'nTitle' => \INFINITY,
        'desc' => \clienttranslate('(artillery units only)'),
        'units' => $artillery,
      ];
    }
  }

  public function getArgsMoveUnits()
  {
    $player = $this->getPlayer();
    $units = Units::getActivatedByCard($this);

    return [
      'units' => $units->map(function ($unit) {
        if ($unit->getType() == \ARTILLERY) {
          return $unit->getPossibleMoves(3, 0);
        } else {
          return $unit->getPossibleMoves();
        }
      }),
    ];
  }

  public function getArgsAttackUnits($overrideNbFights = null)
  {
    return parent::getArgsAttackUnits([\ARTILLERY => 2]);
  }
}
