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
    $this->mustStopWhenEntering = true;
    $this->isImpassableForRetreat = true;
    $this->isBlockingSandbag = true;
    parent::__construct($row);
  }
}
