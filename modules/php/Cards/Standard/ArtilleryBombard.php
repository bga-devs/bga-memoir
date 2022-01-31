<?php
namespace M44\Cards\Standard;

class ArtilleryBombard extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_ARTILLERY_BOMBARD;
    $this->name = clienttranslate('Artillery Bombard');
    $this->text = [
      clienttranslate('Issue an order to all <ARTILLERY> units.'),
      clienttranslate('Units may move up to 3 hexes or battle twice.'),
      clienttranslate('If you do not command any artillery units, issue an order to 1 unit of your choice.'),
    ];
  }
}
