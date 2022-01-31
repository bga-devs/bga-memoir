<?php
namespace M44\Cards\Standard;

class InfantryAssault extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_INFANTRY_ASSAULT;
    $this->name = clienttranslate('Infantry Assault');
    $this->text = [
      clienttranslate(
        'Issue an order to all <INFANTRY> units in 1 section. Units may move up to 2 hexes and still battle, or move 3 hexes but not battle.'
      ),
      clienttranslate('Terrain movement and battle restrictions still apply.'),
      clienttranslate('If you do not command any infantry units, issue an order to 1 unit of your choice.'),
    ];
  }
}
