<?php
namespace M44\Terrains;
use M44\Board;

class FieldBunker extends Bunker
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['casemate', 'wbunker', 'dbunker', 'pbunker']);
  }

  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Field bunkers');
    $this->number = 22;
    $this->desc = [clienttranslate('Protect any occupant'), clienttranslate('Bunkers have 360° arc of fire')];
  }

  public function isOriginalOwner($unit)
  {
    return false;
  }

  public function canIgnoreOneFlag($unit)
  {
    return $this->canIgnoreOneFlag;
  }
}
