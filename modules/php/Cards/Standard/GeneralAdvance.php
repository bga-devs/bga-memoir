<?php
namespace M44\Cards\Standard;

class GeneralAdvance extends \M44\Models\SectionCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_GENERAL_ADVANCE;
    $this->name = clienttranslate('General advance');
    $this->subtitle = clienttranslate('Issue an order to 2 units in each section');
    $this->text = [clienttranslate('Order 2 Units in each section')];
    $this->sections = [2, 2, 2];
    $this->nUnits = 6;
  }

  public function getNotifString()
  {
    return null;
  }

  public function getArgsOrderUnits()
  {
    $data = parent::getArgsOrderUnits();
    $data['nTitle'] = 2;
    return $data;
  }

  public function getOrderUnitsTitle($val, $marineCommand)
  {
    return $marineCommand
      ? clienttranslate('in each section plus one additional unit')
      : clienttranslate('in each section');
  }
}
