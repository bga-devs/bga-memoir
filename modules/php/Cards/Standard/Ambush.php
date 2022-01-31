<?php
namespace M44\Cards\Standard;

class Ambush extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = CARD_AMBUSH;
    $this->name = clienttranslate('Ambush');
    $this->text = [
      clienttranslate('After opponent declares a Close Assault, but before he rolls his battle dice, play this card.'),
      clienttranslate('Roll your battle dice first.'),
      clienttranslate("If opponent's unit wasn't eliminated or forced to retreat, it may then attack normally."),
      clienttranslate('At the end of the turn, draw your Command card first.'),
    ];
  }
}
