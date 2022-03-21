<?php
namespace M44\Terrains;
use M44\Board;

class HighGround extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['highground']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('High Ground');
    $this->number = 25;
    $this->desc = [
      \clienttranslate('No movements restriction'),
      clienttranslate('No combat restriction'),
      clienttranslate(
        'In Flooded fields scenarios, Hills, Roads, Railways, Towns & Villages are all considererd High Ground, but keep their standard effects'
      ),
      clienttranslate('Does not block line of sight'),
    ];
    parent::__construct($row);
  }
}
