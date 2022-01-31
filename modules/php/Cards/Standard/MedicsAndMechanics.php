<?php
namespace M44\Cards\Standard;

class MedicsAndMechanics extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_MEDICS;
    $this->name = clienttranslate('Medics & Mechanics');
    $this->text = [
      clienttranslate('Issue an order to 1 unit that has suffered casualties.'),
      clienttranslate('Roll 1 battle die for each command card you have, including this card.'),
      clienttranslate(
        'For each die matching the unit\'s symbol or a star, 1 lost figure of that unit is returned to duty. A unit may not gain more figures than it originally had.'
      ),
      clienttranslate('If the unit recovers at least 1 figure, it may also be issued an order.'),
    ];
  }
}
