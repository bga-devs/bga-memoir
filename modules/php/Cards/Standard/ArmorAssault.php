<?php
namespace M44\Cards\Standard;

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
    $units = $player->getUnits();

    // Keep only armor
    $armors = $units->filter(function ($unit) {
      return $unit->getType() == \ARMOR;
    });

    if ($armors->empty()) {
      // No armor => 1 unit of your choice
      return [
        'i18n' => ['desc'],
        'n' => 1,
        'nTitle' => 1,
        'desc' => \clienttranslate('(because no armor units)'),
        'units' => $units->getPositions(),
      ];
    } else {
      return [
        'i18n' => ['desc'],
        'n' => 4,
        'nTitle' => 4,
        'desc' => \clienttranslate('(armor units only)'),
        'units' => $armors->getPositions(),
      ];
    }
  }

  public function getDiceModifier($unit, $cell)
  {
    // Bonus dice is only for close combat & for Armor
    return $unit->getType() == \ARMOR && $cell['d'] == 1 ? 1 : 0;
  }
}
