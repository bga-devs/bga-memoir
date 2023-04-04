<?php
namespace M44\Units;

class Sniper extends Infantry
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Sniper');
    $this->number = 10;
    $this->movementAndAttackRadius = 2;
    $this->maxUnits = 1;
    $this->medalsWorth = 0;
    $this->attackPower = [1, 1, 1, 1, 1];
    $this->retreatHex = 3;
    $this->ignoreCannotBattle = true;
    $this->ignoreDefense = true;
    $this->targets[ARMOR] = false;
    $this->targets[DESTROYER] = false;
    $this->desc = [
      clienttranslate('Retreat up to 3 hex/flag'),
      clienttranslate('Move onto any terrain and may still battle, but must obey terrain movement restrictions'),
      clienttranslate('May not target an Armor unit'),
      clienttranslate('Hit enemy on symbol, star & grenade, ignore terrain protection'),
      clienttranslate('Sniper is only hit by a grenade (& star exceptions)'),
      clienttranslate('Sniper does not count as Victory medal'),
    ];
    $this->applyPropertiesModifiers();
  }

  public function getHits($type, $nb)
  {
    if ($type == \DICE_GRENADE) {
      return $nb;
    }

    return 0;
  }

  // used to get additional hits from special power of unit (sniper, etc.)
  public function getHitsOnTarget($type, $nb, $target)
  {
    if ($type == \DICE_STAR) {
      return $nb;
    }
    return -1; // to keep hits done on the unit (normal resolution)
  }
}
