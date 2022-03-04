<?php
namespace M44\Cards\Standard;
use M44\Board;
use M44\Helpers\Collection;
use M44\Helpers\Utils;

class CloseAssault extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_CLOSE_ASSAULT;
    $this->name = clienttranslate('Close Assault');
    $this->text = [
      clienttranslate('Issue an order to all <INFANTRY> and/or <ARMOR> units adjacent to enemy units.'),
      clienttranslate(
        'Units ordered battle with 1 additional die. Units may not move before they battle, but, after a successful Close Assault, they may Take Ground and Armor units may make an Armor Overrun.'
      ),
    ];
  }

  public function getArgsOrderUnits()
  {
    $player = $this->getPlayer();
    $units = $player->getUnits()->filter(function ($unit) {
      return in_array($unit->getType(), [INFANTRY, ARMOR]) && Board::isAdjacentToEnnemy($unit);
    });

    return [
      'n' => \INFINITY,
      'nTitle' => '',
      'desc' => '',
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
    foreach ($args['units'] as &$cells) {
      Utils::filter($cells, function ($cell) {
        if (isset($cell['type'])) {
          return true;
        } else {
          return $cell['d'] == 1;
        }
      });
    }

    return $args;
  }

  public function getDiceModifier($unit, $cell)
  {
    // Bonus dice is only for first assault, not for armor overrun
    return $unit->getFights() == 0 ? 1 : 0;
  }
}
