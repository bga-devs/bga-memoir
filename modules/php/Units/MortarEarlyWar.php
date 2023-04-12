<?php
namespace M44\Units;
use M44\Board;

class MortarEarlyWar extends Infantry
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->number = '3c';
    $this->name = clienttranslate('Mortar (Early War) 1939-42');
    $this->movementRadius = 2;
    $this->movementAndAttackRadius = 0;
    $this->maxGrounds = 0; // unit with equipment cannot take ground
    $this->cannotBattleIfMoved = true;
    $this->ignoreDefense = true;
    $this->mustSeeToAttack = false;
    $this->banzai = false; // even if japanese infantry (FAQ)
    $this->isSWAEquipped = true;
    $this->desc = [
      clienttranslate('Fires like the infantry unit it equips'),
      //clienttranslate('May only move 1-2 or battle'),
      clienttranslate('May not Take Ground'),
      clienttranslate('Ignores line of sight'),
    ];
    $this->applyPropertiesModifiers();
    //$this->desc[] = clienttranslate('');
  }
}
