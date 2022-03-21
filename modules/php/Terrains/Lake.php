<?php
namespace M44\Terrains;

class Lake extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['lakeA', 'lakeB', 'lakeC']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Lake');
    $this->number = 26;
    $this->desc = [
      clienttranslate('Impassable'),
      clienttranslate('Two or more contiguous adjacent Lake hexes block the line of sight'),
    ];
    $this->isImpassable = true;

    parent::__construct($row);
  }

  public function isBlockingLineOfSight($unit, $target, $path)
  {
    //TODO
    return false;
  }
}
