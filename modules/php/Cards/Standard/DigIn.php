<?php
namespace M44\Cards\Standard;
use M44\Managers\Units;
use M44\Managers\Terrains;
use M44\Board;
use M44\Core\Notifications;
use M44\Core\Globals;

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
    $marineCommand = $player->isMarineCommand();
    $units = $player->getUnits();

    // Keep only infantry on board only (not on Staging area)
    $infantry = $units->filter(function ($unit) {
      return $unit->getType() == \INFANTRY && !$unit->isOnReserveStaging();
    });

    if ($infantry->empty()) {
      // No infantry => 1 unit of your choice
      $unitstmp = $units->filter(function ($unit) {
        return (!($unit -> getExtraDatas('cannotBeActivatedUntilTurn') >= Globals::getTurn()));
      });
      return [
        'i18n' => ['desc'],
        'n' => $marineCommand ? 2 : 1,
        'nTitle' => $marineCommand ? 2 : 1,
        'desc' => \clienttranslate('(because no infantry units)'),
        'units' => $unitstmp,
      ];
    } else {
      return [
        'i18n' => ['desc'],
        'n' => $marineCommand ? 5 : 4,
        'nTitle' => $marineCommand ? 5 : 4,
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

        Notifications::addTerrain(
          $player,
          $terrain,
          \clienttranslate('${player_name} reinforces their position by placing a sandbag (in ${coordSource})')
        );
      }
    }
  }
}
