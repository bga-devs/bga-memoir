<?php
namespace M44\Cards\Overlord;

class InfantryAssault extends \M44\Cards\Standard\InfantryAssault
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->text[0] = clienttranslate(
      'Issue an order to all <INFANTRY> units in 1 section OR the other. Units may move up to 2 hexes and still battle, or move 3 hexes but not battle.'
    );
  }
}
