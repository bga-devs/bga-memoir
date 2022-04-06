<?php
namespace M44\Cards\Standard;

class DirectFromHQ extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_DIRECT_FROM_HQ;
    $this->name = clienttranslate('Direct From HQ');
    $this->text = [clienttranslate('Issue an order to 4 units of your choice.')];
  }

  public function getArgsOrderUnits()
  {
    $player = $this->getPlayer();
    $units = $player->getUnits()->getPositions();

    return [
      'n' => 4,
      'nTitle' => 4,
      'nOnTheMove' => 0,
      'desc' => '',
      'sections' => [\INFINITY, \INFINITY, INFINITY],
      'units' => $units,
    ];
  }
}
