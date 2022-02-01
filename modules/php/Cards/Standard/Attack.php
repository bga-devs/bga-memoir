<?php
namespace M44\Cards\Standard;

class Attack extends \M44\Models\SectionCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = CARD_ASSAULT;
    $this->name = clienttranslate('Attack');
    $this->subtitles = [
      clienttranslate('Issue an order to 3 units on the Left Flank'),
      clienttranslate('Issue an order to 3 units in the Center'),
      clienttranslate('Issue an order to 3 units on the Right Flank')
    ];
    $this->text = [clienttranslate('Order 3 Units')];
    $this->nUnits = 3;
  }
}
