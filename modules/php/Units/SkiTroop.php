<?php
namespace M44\Units;

class SkiTroop extends Infantry
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Ski Troops');
    $this->number = 9;
    $this->movementAndAttackRadius = 3;
    $this->maxUnits = 3;
    $this->movementRadius = 3;
    $this->attackPower = [3, 2];
    $this->ignoreCannotBattle = true;
    $this->retreatHex = 3;
    $this->desc = [
      clienttranslate('3 figures'),
      clienttranslate('Fire at 3, 2'),
      clienttranslate('Move onto any terrain and may still battle, but must still obey terrain movement restrictions'),
      clienttranslate('May retreat 1 to 3 hexes on flag'),
    ];
    $this->applyPropertiesModifiers();
  }
}
