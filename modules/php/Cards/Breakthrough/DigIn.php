<?php
namespace M44\Cards\Breakthrough;

class DigIn extends \M44\Cards\Standard\DigIn
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->text = [
      clienttranslate('Issue an order to 4 <INFANTRY> and/or <ARTILLERY> units.'),
      clienttranslate('The units improve their position by placing an available sandbag on the units\' hexes.'),
      clienttranslate(
        'If you do not command any infantry or artillery units, issue an order to 1 unit of your choice.'
      ),
    ];
  }
}
