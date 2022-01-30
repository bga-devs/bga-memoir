<?php
namespace M44\Cards\Standard;

class Recon extends \M44\Models\SectionCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_RECON;
    $this->name = clienttranslate('Recon');
    $this->subtitles = [
      clienttranslate('Issue an order to 1 unit on the Left Flank'),
      clienttranslate('Issue an order to 1 unit in the Center'),
      clienttranslate('Issue an order to 1 unit on the Right Flank'),
    ];
    $this->text = [
      clienttranslate('Order 1 Unit'),
      clienttranslate('When drawing a new Commander card, draw two, choose one and discard the other'),
    ];
    $this->nUnits = 1;
  }
}
