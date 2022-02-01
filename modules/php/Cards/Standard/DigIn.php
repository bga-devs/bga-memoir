<?php
namespace M44\Cards\Standard;

class DigIn extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_DIG_IN;
    $this->name = clienttranslate('Dig In');
    $this->text = [
      clienttranslate('Issue an order to 4 <INFANTRY> units.'),
      clienttranslate('The units improve their position by placing an available sandbag on the units\' hexes.'),
      clienttranslate('If you do not command any infantry units, issue an order to 1 unit of your choice.'),
    ];
  }
}
