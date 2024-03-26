<?php
namespace M44\Units;

class Locomotive extends AbstractUnit
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = LOCOMOTIVE;
    $this->number = 5;
    $this->statName = 'Other';
    $this->name = clienttranslate('Trains');
    $this->maxUnits = 4;
    $this->movementRadius = 3;
    $this->movementAndAttackRadius = 3;
    //$this->attackPower = []; mettre une condition en fonction du type de train
    $this->cannotHeal = true;
    //$this->canIgnoreOneFlag = true; 
    $this->desc = [
      clienttranslate(
        'Can be activated as a whole unit'
      ),
      clienttranslate(
        'May ingore 1 flag, may retreat in backward way of the locomotive'
      ),
      clienttranslate(
        'Hit only on Grenade'
      ),
      clienttranslate(
        'Car is destroyed on 3rd hit, locomotive on 4th hit'
      ),
      clienttranslate(
        'Move back or forward, if railroad is not blocked'
      ),
    ];
    $this->applyPropertiesModifiers();
  }
}
