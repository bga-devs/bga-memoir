<?php
namespace M44\Units;

class FrenchResistance extends AbstractUnit
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = INFANTRY;
    $this->number = '1d';
    $this->statName = 'Inf';
    $this->name = clienttranslate('French Resistance');
    $this->maxUnits = 3;
    $this->movementRadius = 2;
    $this->movementAndAttackRadius = 1;
    $this->attackPower = [3, 2, 1];
    $this->mustSeeToAttack = true;
    $this->maxGrounds = 1;
    $this->ignoreCannotBattle = true;
    $this->retreatHex = 3;
    $this->cannotHeal = false;
    $this->desc = [
      clienttranslate('3 figures only'),
      clienttranslate('May retreat 1 to 3 hexes per flag'),
      \clienttranslate('Move onto any terrain and may still battle, but must still obey terrain movement restrictions'),
    ];
    $this->applyPropertiesModifiers();
  }
}
