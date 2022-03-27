<?php
namespace M44\Terrains;
use M44\Board;

class Escarpment extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['descarpment']);
  }

  public function __construct($row)
  {
    parent::__construct($row);

    $this->name = clienttranslate('Escarpment');
    $this->number = 63;
    $this->desc = [clienttranslate('Impassable terrain to all units'), clienttranslate('Blocks line of sight')];
    $this->isImpassable = true;
    $this->isBlockingLineOfSight = true;
  }
}
