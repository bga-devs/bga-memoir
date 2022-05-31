<?php
namespace M44\Cards\Standard;

class ReconInForce extends \M44\Models\SectionCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = CARD_RECON_IN_FORCE;
    $this->name = clienttranslate('Recon in force');
    $this->subtitle = clienttranslate('Issue an order to 1 unit in each section');
    $this->text = [clienttranslate('Order 1 Unit in each section')];
    $this->nUnits = 3;
    $this->sections = [1, 1, 1];
  }

  public function getArgsOrderUnits()
  {
    $data = parent::getArgsOrderUnits();
    $data['nTitle'] = 1;
    return $data;
  }

  public function getOrderUnitsTitle($val, $marineCommand)
  {
    return $marineCommand
      ? clienttranslate('in each section plus one additional unit')
      : clienttranslate('in each section');
  }
}
