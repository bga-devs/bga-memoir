<?php
namespace M44\Cards\Standard;
use M44\Managers\Units;
use M44\Managers\Terrains;
use M44\Board;
use M44\Core\Notifications;

class DigIn extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_DIG_IN;
    $this->name = clienttranslate('Dig In');
    $this->text = [
      clienttranslate('Issue an order to 4 <INFANTRY> units.'),
      clienttranslate('The units improve their position by placing an available sandbag on the units\' hexes.'),
      clienttranslate('If you do not command any infantry units, issue an order to 1 unit of your choice.'),
    ];
  }

  public function getArgsOrderUnits()
  {
    $player = $this->getPlayer();
    $units = $player->getUnits();

    // Keep only infantry
    $infantry = $units->filter(function ($unit) {
      return $unit->getType() == \INFANTRY;
    });

    if ($infantry->empty()) {
      // No infantry => 1 unit of your choice
      return [
        'i18n' => ['desc'],
        'n' => 1,
        'nTitle' => 1,
        'desc' => \clienttranslate('(because no infantry units)'),
        'units' => $units,
      ];
    } else {
      return [
        'i18n' => ['desc'],
        'n' => 4,
        'nTitle' => 4,
        'desc' => \clienttranslate('(for improving positions)'),
        'units' => $infantry,
      ];
    }
  }

  public function nextStateAfterOrder($unitIds, $onTheMoveIds)
  {
    if (count($unitIds) == 1 && Units::get($unitIds)->getType() != \INFANTRY) {
      return 'moveUnits';
    } else {
      return 'digIn';
    }
  }

  public function stDigIn()
  {
    $player = $this->getPlayer();
    $units = Units::getActivatedByCard($this);
    foreach ($units as $unit) {
      if (Board::canPlaceSandbag($unit)) {
        $terrain = Terrains::add([
          'type' => 'sand',
          'tile' => 'sand',
          'x' => $unit->getX(),
          'y' => $unit->getY(),
          'orientation' => ($unit->getCampDirection() + 3) / 2,
        ]);

        Notifications::addObstacle(
          $player,
          $terrain,
          \clienttranslate('${player_name} reinforces their position by placing a sandbag (in ${coordSource})')
        );
      }
    }
  }
}
