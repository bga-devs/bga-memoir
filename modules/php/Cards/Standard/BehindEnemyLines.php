<?php
namespace M44\Cards\Standard;

class BehindEnemyLines extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_BEHIND_LINES;
    $this->name = clienttranslate('Behind Enemy Lines');
    $this->text = [
      clienttranslate('Issue an order to 1 <INFANTRY> unit.'),
      clienttranslate('Unit may move up to 3 hexes.'),
      clienttranslate('Unit may battle with 1 additional die, then move again up to 3 hexes.'),
      clienttranslate('Terrain movement restrictions are ignored.'),
      clienttranslate('Terrain battle restrictions still apply.'),
      clienttranslate('If you do not command any infantry units, issue an order to 1 unit of your choice.'),
    ];
  }
}
