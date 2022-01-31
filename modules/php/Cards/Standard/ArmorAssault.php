<?php
namespace M44\Cards\Standard;

class ArmorAssault extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_ARMOR_ASSAULT;
    $this->name = clienttranslate('Armor Assault');
    $this->text = [
      clienttranslate('Issue an order to 4 <ARMOR> units.'),
      clienttranslate('Units in Close Assault roll 1 additional die.'),
      clienttranslate('Terrain movement and battle restrictions still apply.'),
      clienttranslate('If you do not command any armor units, issue an order to 1 unit of your choice.'),
    ];
  }
}
