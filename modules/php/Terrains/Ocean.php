<?php
namespace M44\Terrains;


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

  public function isValidPath($unit, $cell, $path)
  {
    // destroyer can only move on row 8 with high water
    if($unit->getType() == DESTROYER) {
      return $this->getPos()['y'] == 8;
    }
    else {
      return true;
    }
  }

}

