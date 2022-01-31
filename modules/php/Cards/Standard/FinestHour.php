<?php
namespace M44\Cards\Standard;

class FinestHour extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_FINEST_HOUR;
    $this->name = clienttranslate('Their Finest Hour');
    $this->text = [
      clienttranslate('Roll 1 battle die for each command card you have, including this card.'),
      clienttranslate(
        'For each unit symbol rolled, 1 unit of this type is ordered. For each star rolled, 1 unit of your choice is ordered.'
      ),
      clienttranslate('Ordered units battle with 1 additional die.'),
      clienttranslate('Reshuffle the deck and discard pile.'),
    ];
  }
}
