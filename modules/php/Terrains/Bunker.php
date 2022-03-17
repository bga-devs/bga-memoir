<?php
namespace M44\Terrains;
use M44\Board;

class Bunker extends \M44\Models\RectTerrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['bunker']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Bunkers');
    $this->number = 2;
    $this->isImpassable = [ARMOR, \ARTILLERY];
    $this->isBlockingLineOfSight = true;
    $this->canIgnoreOneFlag = true;
    $this->defense = [\INFANTRY => -1, ARMOR => -2];
    $this->cantLeave = [\ARTILLERY];
    $this->isBunker = true;

    parent::__construct($row);
  }

  public function defense($unit)
  {
    if (!$this->isOriginalOwner($unit)) {
      return $this->defense[$unit->getType()] ?? 0;
    } else {
      return 0;
    }
  }

  protected function isOriginalOwner($unit)
  {
    return $unit
      ->getPlayer()
      ->getTeam()
      ->getId() == $this->getExtraDatas('owner');
  }

  public function canIgnoreOneFlag($unit)
  {
    return $this->isOriginalOwner($unit);
  }
}
