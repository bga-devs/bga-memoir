<?php
namespace M44\Cards\Standard;

class Barrage extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_BARRAGE;
    $this->name = clienttranslate('Barrage');
    $this->text = [
      clienttranslate('Target any 1 enemy unit.'),
      clienttranslate('Roll 4 battle dice, ignoring any terrain battle die reduction.'),
      clienttranslate("Score 1 hit for each die matching the unit's symbol or grenade."),
      clienttranslate('For each flag, retreat 1 hex.'),
      clienttranslate('Flags may not be ignored.'),
    ];
  }
}
