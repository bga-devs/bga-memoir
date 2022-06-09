<?php
namespace M44\Units;

class HeavyAntiTankGun extends Artillery
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Heavy Anti-Tank Guns');
    $this->number = '23';
    $this->attackPower = [2, 2, 2, 2];
    $this->mustSeeToAttack = true;
    $this->ignoreDefense = true;
    $this->applyPropertiesModifiers();
    $this->desc[] = clienttranslate('Stars hit on Armor or Vehicles');
    $this->desc[] = clienttranslate('Require line of sight to target enemy unit');
  }

  public function getHitsOnTarget($type, $nb, $target)
  {
    if ($target->getType() == ARMOR && $type == \DICE_STAR) {
      return $nb;
    }
    return -1; // to keep hits done on the unit (normal resolution)
  }
}
