<?php
namespace M44\Cards\Standard;

class PincerMove extends \M44\Models\SectionCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_PINCER_MOVE;
    $this->name = clienttranslate('Pincer move');
    $this->subtitle = clienttranslate('Issue an order to 2 units on the Left Flank and 2 units on the Right Flank');
    $this->text = [clienttranslate('Order 2 Units on each flank')];
    $this->sections = [2, 0, 2];
  }
}
