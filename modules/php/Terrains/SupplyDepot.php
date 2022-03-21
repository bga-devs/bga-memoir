<?php
namespace M44\Terrains;

class SupplyDepot extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['depot']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Supply depots');
    $this->number = 43;

    $this->desc = [
      clienttranslate('No movement restriction'),
      clienttranslate('No combat restriction'),
      clienttranslate('Block line of sight'),
    ];

    $this->isBlockingLineOfSight = true;
    parent::__construct($row);
  }
}
