<?php
namespace M44\Cards\Overlord;

class AirPower extends \M44\Cards\Standard\AirPower
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->text = [
      clienttranslate(
        'Target 4 or fewer contiguously adjacent enemy ground units. Ignoring any terrain battle die reduction, roll 2d against each (if Allied) or 1d against each (if Axis).'
      ),
      clienttranslate("Score 1 hit for each die that matches a unit’s symbol,
a grenade or a star. Flags may not be ignored.
For each flag rolled, unit must retreat 1 hex."),
      clienttranslate('Play this card at the start of the turn, before your Field Generals play any of their cards.'),
    ];
  }
}
