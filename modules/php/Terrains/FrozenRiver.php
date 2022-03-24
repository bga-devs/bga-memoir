<?php
namespace M44\Terrains;
use M44\Board;

class FrozenRiver extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['wriver', 'wriverFR', 'wcurved']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Frozen Rivers');
    $this->number = 47;

    parent::__construct($row);
  }

  public function onUnitEntering($unit, $isRetreat)
  {
    die('TODO: implement Frozn River');
  }
}
