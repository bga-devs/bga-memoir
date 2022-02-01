<?php
namespace M44\Cards\Standard;

class Probe extends \M44\Models\SectionCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_PROBE;
    $this->name = clienttranslate('Probe');
    $this->subtitles = [
      clienttranslate('Issue an order to 2 units on the Left Flank'),
      clienttranslate('Issue an order to 2 units in the Center'),
      clienttranslate('Issue an order to 2 units on the Right Flank')
    ];
    $this->text = [clienttranslate('Order 2 Units')];
    $this->nUnits = 2;
  }
}
