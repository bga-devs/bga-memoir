<?php
namespace M44\Cards\Standard;

class Assault extends \M44\Models\SectionCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = CARD_ASSAULT;
    $this->name = clienttranslate('Assault');
    $this->subtitles = [
      clienttranslate('Issue an order to all units on the Left Flank'),
      clienttranslate('Issue an order to all units in the Center'),
      clienttranslate('Issue an order to all units on the Right Flank')
    ];
    $this->text = [clienttranslate('Order ALL Units')];
    $this->nUnits = \INFINITY;
  }
}
