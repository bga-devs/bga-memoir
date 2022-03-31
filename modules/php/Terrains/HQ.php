<?php
namespace M44\Terrains;

class HQ extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['dcamp']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('HQ & Supply tents');
    $this->number = 56;

    $this->desc = [
      clienttranslate('No movement restriction'),
      clienttranslate('No combat restriction'),
      clienttranslate('Block line of sight'),
    ];

    $this->isBlockingLineOfSight = true;
    parent::__construct($row);
  }

  // TODO: scenario specific / control with owner of HQ
}
