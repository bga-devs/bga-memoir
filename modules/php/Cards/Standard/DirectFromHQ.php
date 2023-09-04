<?php
namespace M44\Cards\Standard;
use M44\Core\Globals;

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
    $marineCommand = $player->isMarineCommand();
    $units = $player->getUnits();
    $unitstmp = $units->filter(function ($unit) {
      return (!($unit -> getExtraDatas('cannotBeActivatedUntilTurn') >= Globals::getTurn()));
    });

    return [
      'n' => $marineCommand ? 5 : 4,
      'nTitle' => $marineCommand ? 5 : 4,
      'nOnTheMove' => 0,
      'desc' => '',
      'sections' => [\INFINITY, \INFINITY, INFINITY],
      'units' => $unitstmp->getPositions(),
    ];
  }
}
