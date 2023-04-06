<?php
namespace M44\Units;
use M44\Board;

class Infantry extends AbstractUnit
{
  public function __construct($row)
  {
    $this->type = INFANTRY;
    $this->number = 1;
    $this->statName = 'Inf';
    $this->name = clienttranslate('Infantry');
    $this->maxUnits = 4;
    $this->movementRadius = 2;
    $this->movementAndAttackRadius = 1;
    $this->attackPower = [3, 2, 1];
    $this->mustSeeToAttack = true;
    $this->maxGrounds = 1;
    $this->cannotHeal = false;
    $this->cannotBattleBack = false;
    parent::__construct($row);
  }

  public function getAttackModifier($target)
  {
    if ($this->getBonusCloseAssault() == true && !$this->isWounded() && $target['d'] == 1) {
      return 1;
    }

    // if unit is on a boat & on a river, malus of 1
    if ($this->getEquipment() == 'boat' && Board::isRiverCell(['x' => $this->x, 'y' => $this->y])) {
      return -1;
    }
    return 0;
  }

  public function getHits($type, $nb)
  {
    $hits = parent::getHits($type, $nb);
    // if the unit is in a boat on a river
    if (
      $this->getEquipment() == 'boat' &&
      Board::isRiverCell(['x' => $this->x, 'y' => $this->y]) &&
      $type == \DICE_FLAG
    ) {
      $hits += $nb;
    }
    return $hits;
  }
}
