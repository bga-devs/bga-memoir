<?php
namespace M44\Terrains;

use M44\Board;


class Ocean extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return false;
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Oceans & Shorelines');
    $this->number = 7;
    $this->cannotBattle = true;
    // destroyer can thus move without any limitation on ocean
    $this->mustStopWhenEntering = [\INFANTRY, \ARMOR, \ARTILLERY]; 
    $this->isImpassableForRetreat = true;
    $this->isBlockingSandbag = true;

    parent::__construct($row);
  }
}

