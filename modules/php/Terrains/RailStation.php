<?php
namespace M44\Terrains;

class RailStation extends Village
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['station']);
  }

  public function __construct($row)
  {
    parent::__construct($row);

    $this->name = clienttranslate('Railroad station');
    $this->number = 39;

    $this->mustStopWhenEntering = true;
    $this->enteringCannotBattle = true;
    $this->isBlockingLineOfSight = true;
    $this->defense = [INFANTRY => -1, ARMOR => -2];
    $this->offense = [ARMOR => -2];
  }
}
