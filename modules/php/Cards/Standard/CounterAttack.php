<?php
namespace M44\Cards\Standard;

class CounterAttack extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_COUNTER_ATTACK;
    $this->name = clienttranslate('CounterAttack');
    $this->text = [
      clienttranslate('Issue the same order just played by your opponent.'),
      clienttranslate(
        'When countering a section card, a right section card becomes the equivalent left section card, and vice-versa.'
      ),
      clienttranslate(
        'When counter-attacking an Infantry Assault card, the counter-attack must occur in the same section as your opponent.'
      ),
    ];
  }
}
