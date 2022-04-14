<?php
namespace M44\Units;
use M44\Board;

class MobileArtillery extends Artillery
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->number = '14';
    $this->name = clienttranslate('Mobile Artillery');
    $this->movementRadius = 2;
    $this->movementAndAttackRadius = 1;
    $this->cannotBattleIfMoved = false;
  }
}
