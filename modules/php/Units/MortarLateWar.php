<?php
namespace M44\Units;
use M44\Board;

class MortarLateWar extends Infantry
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->number = '6c';
    $this->name = clienttranslate('Mortar (Late War >1942)');
    $this->movementRadius = 2;
    $this->movementAndAttackRadius = 1;
    $this->attackPower = [3, 2, 1, 1]; // by default if not moved, if moved [3, 2, 1] like infantry
    $this->maxGrounds = 0; // unit with equipment cannot take ground
    $this->ignoreDefense = true; // by default if not moved
    $this->mustSeeToAttack = false;// by default if not moved
    $this->desc = [
      clienttranslate('Treated as Infantry for all purposes'),
      clienttranslate('when it move battle like infantry 3/2/1'),
      clienttranslate('May not Take Ground'),
      clienttranslate('In addition when it does not move, increase range to 3/2/1/1 
      and ignore line of sight and terrains protections'),
      clienttranslate('when it move battle like infantry 3/2/1'),
      ];
    $this->applyPropertiesModifiers();
  }

  public function getAttackPower($pos)
  {
    if($this->getMoves() == 0 && $pos == $this->getpos()) {
      $this->ignoreDefense = true; // by default if not moved TO DO
      $this->mustSeeToAttack = false; 
      return [3, 2, 1, 1];
    } else { // if unit moved is like an infantery
      $this->ignoreDefense = false; // by default if not moved TO DO
      $this->mustSeeToAttack = true; 
      return [3, 2, 1]; 
    }
  }

  // reset original properties after attack
  public function afterAttack($coords, $hits, $eliminated)
  {
    $this->ignoreDefense = true;
    $this->mustSeeToAttack = false; 
    $this->attackPower = [3, 2, 1, 1];
  }

}
