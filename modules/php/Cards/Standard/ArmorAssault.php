<?php
namespace M44\Cards\Standard;
use M44\Core\Globals;

class ArmorAssault extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_ARMOR_ASSAULT;
    $this->name = clienttranslate('Armor Assault');
    $this->text = [
      clienttranslate('Issue an order to 4 <ARMOR> units.'),
      clienttranslate('Units in Close Assault roll 1 additional die.'),
      clienttranslate('Terrain movement and battle restrictions still apply.'),
      clienttranslate('If you do not command any armor units, issue an order to 1 unit of your choice.'),
    ];
  }

  public function getArgsOrderUnits()
  {
    $player = $this->getPlayer();
    $marineCommand = $player->isMarineCommand();

    $units = $player->getUnits();

    // Keep only armor
    $armors = $units->filter(function ($unit) {
      return $unit->getType() == \ARMOR;
    });

    if ($armors->empty()) {
      // No armor => 1 unit of your choice
      $unitstmp = $units->filter(function ($unit) {
        return (!($unit -> getExtraDatas('cannotBeActivatedUntilTurn') >= Globals::getTurn()));
      });
      return [
        'i18n' => ['desc'],
        'n' => $marineCommand ? 2 : 1,
        'nTitle' => $marineCommand ? 2 : 1,
        'desc' => \clienttranslate('(because no armor units)'),
        'units' => $unitstmp->getPositions(),
      ];
    } else {
      $armorstmp = $armors->filter(function ($unit) {
        return (!($unit -> getExtraDatas('cannotBeActivatedUntilTurn') >= Globals::getTurn()));
      });
      return [
        'i18n' => ['desc'],
        'n' => $marineCommand ? 5 : 4,
        'nTitle' => $marineCommand ? 5 : 4,
        'desc' => \clienttranslate('(armor units only)'),
        'units' => $armorstmp->getPositions(),
      ];
    }
  }

  public function getDiceModifier($unit, $cell)
  {
    // Bonus dice is only for close combat & for Armor
    return $unit->getType() == \ARMOR && $cell['d'] == 1 ? 1 : 0;
  }
}
