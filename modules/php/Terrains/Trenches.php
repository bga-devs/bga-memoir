<?php
namespace M44\Terrains;

class Trenches extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['wtrenches', 'ptrenches']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Trenches');
    $this->number = 51;

    $this->mustStopWhenEntering = true;
    $this->isImpassable = [ARTILLERY];
    $this->cannotBattle = [ARMOR];
    $this->canIgnoreOneFlag = [INFANTRY];
    $this->defense = [INFANTRY => -1, ARMOR => -1];
    parent::__construct($row);
  }
}
