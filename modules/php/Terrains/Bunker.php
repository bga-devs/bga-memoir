<?php
namespace M44\Terrains;

class Bunker extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'pbunker';
    $this->name = clienttranslate('Bunker');
    $this->landscape = 'jungle';
    $this->bunker = true;

    $this->impassable = [ARMOR, INFANTRY];
    $this->blockLineOfSight = true;
    $this->defense = [INFANTRY => -1, ARMOR => -2];
  }

  public function getDefense()
  {
    return $this->defense; // TODO : bonus only apply to owner of bunker
  }
}
