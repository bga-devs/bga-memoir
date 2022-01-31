<?php
namespace M44\Cards\Standard;

class CloseAssault extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_CLOSE_ASSAULT;
    $this->name = clienttranslate('Close Assault');
    $this->text = [
      clienttranslate('Issue an order to all <INFANTRY> and/or <ARMOR> units adjacent to enemy units.'),
      clienttranslate('Units ordered battle with 1 additional die.'),
      clienttranslate(
        ' Units may not move before they battle, but, after a successful Close Assault, they may Take Ground and Armor units may make an Armor Overrun.'
      ),
    ];
  }
}
