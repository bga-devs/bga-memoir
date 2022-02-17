<?php
namespace M44\Terrains;

class Bridge extends \M44\Models\RectTerrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['bridge', 'pbridge', 'railbridge', 'wbridge', 'wrailbridge']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Bridges');
    $this->number = 9;

    parent::__construct($row);
  }
}
