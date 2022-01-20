<?php
namespace M44\Cards;

class ReconInForce implements \M44\Models\SectionCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = CARD_RECON_IN_FORCE;
    $this->name = clienttranslate('Recon in force');
    $this->subtitles = clienttranslate('Issue an order to 1 unit in each section');
    $this->text = clienttranslate('Order 1 Unit in each section');
    $this->sections = [1, 1, 1];
  }
}
