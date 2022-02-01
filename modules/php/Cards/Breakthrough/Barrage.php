<?php
namespace M44\Cards\Breakthrough;

class Barrage extends \M44\Cards\Standard\Barrage
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->text[2] = clienttranslate("Score 1 hit for each die matching the unit's symbol or grenade, or star.");
  }
}
