<?php
namespace M44\Units;

class BigGun extends Artillery
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Big Gun');
    $this->attackPower = [3, 3, 2, 2, 1, 1, 1, 1];
  }

  public function getAttackModifier($target)
  {
  }
}
