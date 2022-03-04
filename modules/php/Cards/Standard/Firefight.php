<?php
namespace M44\Cards\Standard;
use M44\Board;
use M44\Managers\Units;

class Firefight extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_FIREFIGHT;
    $this->name = clienttranslate('Firefight');
    $this->text = [
      clienttranslate('Issue an order to 4 units to open fire.'),
      clienttranslate('Units in a firefight may not be adjacent to an enemy unit, and may not move.'),
      clienttranslate('Firefighting units roll 1 additional die.'),
    ];
  }

  public function getArgsOrderUnits()
  {
    $player = $this->getPlayer();
    $units = $player->getUnits()->filter(function ($unit) {
      return !Board::isAdjacentToEnnemy($unit);
    });

    return [
      'i18n' => ['desc'],
      'n' => 4,
      'nTitle' => 4,
      'desc' => \clienttranslate('(Firefigth)'),
      'units' => $units,
    ];
  }

  public function getArgsMoveUnits()
  {
    return [
      'units' => [],
    ];
  }

  public function getArgsAttackUnits($overrideNbFights = null)
  {
    $args = parent::getArgsAttackUnits($overrideNbFights);
    foreach ($args['units'] as $unitId => &$cells) {
      if (Board::isAdjacentToEnnemy(Units::get($unitId))) {
        $cells = [];
      }
    }

    return $args;
  }

  public function getDiceModifier($unit, $cell)
  {
    return 1;
  }
}
