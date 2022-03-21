<?php
namespace M44\Terrains;
use M44\Board;

class FieldBunker extends Bunker
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['casemate']);
  }

  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Field bunkers');
    $this->number = 22;
  }

  public function isOriginalOwner($unit)
  {
    return true;
  }
}
