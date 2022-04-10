<?php
namespace M44\Terrains;

class AirField extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['airfieldX', 'airfield', 'dairfieldX', 'dairfield', 'pairfield', 'pairfieldX']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Airfields');
    $this->number = 16;
    $this->desc = [
      clienttranslate('When Air rules are in effect airplanes may take-off from, or land on, Airfields'),
      clienttranslate('No movement restrictions'),
      clienttranslate('No combat restrictions'),
      clienttranslate('Do not block line of sight'),
    ];
    parent::__construct($row);
  }
}
