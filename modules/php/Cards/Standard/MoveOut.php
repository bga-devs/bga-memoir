<?php
namespace M44\Cards\Standard;

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
        'desc' => \clienttranslate('(infantry units only)'),
        'units' => $infantry,
      ];
    }
  }
}
