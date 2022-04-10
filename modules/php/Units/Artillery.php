<?php
namespace M44\Units;
use M44\Board;

class Artillery extends AbstractUnit
{
  public function __construct($row)
  {
    $this->type = ARTILLERY;
    $this->number = '1t';
    $this->statName = 'Artillery';
    $this->name = clienttranslate('Artillery');
    $this->maxUnits = 2;
    $this->movementRadius = 1;
    $this->movementAndAttackRadius = 0;
    $this->attackPower = [3, 3, 2, 2, 1, 1];
    $this->mustSeeToAttack = false;
    $this->cannotBattleIfMoved = true;
    parent::__construct($row);
  }

  public function getAttackPower()
  {
    $c = Board::getMountainComponents();
    if (isset($c[$this->x][$this->y]) && $c[$this->x][$this->y] == true) {
      return [3, 3, 2, 2, 1, 1, 1];
    } else {
      return parent::getAttackPower();
    }
  }
}
