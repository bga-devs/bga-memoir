<?php
namespace M44\Cards\Standard;

use M44\Core\Globals;

class MoveOut extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_MOVE_OUT;
    $this->name = clienttranslate('Move Out');
    $this->text = [
      clienttranslate('Issue an order to 4 <INFANTRY> units.'),
      clienttranslate('Terrain movement and battle restrictions still apply.'),
      clienttranslate('If you do not command any infantry units, issue an order to 1 unit of your choice.'),
    ];
  }

  public function getArgsOrderUnits()
  {
    $player = $this->getPlayer();
    $marineCommand = $player->isMarineCommand();
    $units = $player->getUnits();

    // Keep only infantry
    $infantry = $units->filter(function ($unit) {
      return $unit->getType() == \INFANTRY;
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
        'units' => $unitstmp->getPositions(),
      ];
    } else {
      $infantrytmp = $infantry->filter(function ($unit) {
        return (!($unit -> getExtraDatas('cannotBeActivatedUntilTurn') >= Globals::getTurn()));
      });
      return [
        'i18n' => ['desc'],
        'n' => $marineCommand ? 5 : 4,
        'nTitle' => $marineCommand ? 5 : 4,
        'desc' => \clienttranslate('(infantry units only)'),
        'units' => $infantrytmp->getPositions(),
      ];
    }
  }

  public function canArmorBreakthrough() 
  {
    $player = $this->getPlayer();
    $units = $player->getUnits();

    // Keep only infantry
    $infantry = $units->filter(function ($unit) {
      return $unit->getType() == \INFANTRY;
    });
    
    return $infantry->empty();
  }

  public function nextStateAfterPlay()
  {
   if ($this->getExtraDatas('canArmorBreakthrough') === true) {
      return 'armorBreakthrough';
    } else {
      return parent::nextStateAfterPlay();
    }
  }
}
