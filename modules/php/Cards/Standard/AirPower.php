<?php
namespace M44\Cards\Standard;

class AirPower extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = CARD_AIR_POWER;
    $this->name = clienttranslate('Air power');
    $this->text = [
      clienttranslate('Target a group of 4 or fewer enemy units adjacent to each other.'),
      clienttranslate(
        'Roll 2 battle dice per hex (Allied air attack) or 1 per hex (Axis air attack), ignoring any terrain battle die reduction.'
      ),
      clienttranslate("Score 1 hit for each die that matches the unit's symbol, a grenade or a star."),
      clienttranslate('For each flag, retreat 1 hex.'),
      clienttranslate('Flags may not be ignored.'),
    ];
  }
}
