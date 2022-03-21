<?php
namespace M44\Terrains;

class Cemetery extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['cemetery']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Cemeteries');
    $this->number = 18;
    $this->desc = [
      clienttranslate('No movement restrictions'),
      clienttranslate('No combat restrictions'),
      clienttranslate('Unit may ignore one flag'),
      clienttranslate('Do not block line of sight'),
    ];
    $this->canIgnoreOneFlag = true;
    parent::__construct($row);
  }
}
