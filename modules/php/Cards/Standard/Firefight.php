<?php
namespace M44\Cards\Standard;

class Firefight extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_FIREFIGHT;
    $this->name = clienttranslate('Firefight');
    $this->text = [
      clienttranslate('Issue an order to 4 units to open fire.'),
      clienttranslate('Units in a firefight may not be adjacent to an enemy unit, and may not move.'),
      clienttranslate('Firefighting units roll 1 additional die.'),
    ];
  }
}
