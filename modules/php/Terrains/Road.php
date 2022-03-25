<?php
namespace M44\Terrains;
use M44\Board;

class Road extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['road', 'roadcurve', 'roadFL', 'roadFR', 'roadX', 'roadY', 'hillroad', 'hillcurve']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Roads');
    $this->number = 42;
    $this->desc = [
      \clienttranslate('Unit that starts its move on a Road and stays on it may move 1 additional hex'),
      clienttranslate('No combat restriction'),
      clienttranslate('Do not block line of sight'),
    ];
    parent::__construct($row);
  }
  // TODO : road managementt
}
