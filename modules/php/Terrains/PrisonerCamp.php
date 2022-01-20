<?php
namespace M44\Terrains;

class PrisonerCamp extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'camp';
    $this->name = clienttranslate('Prisoner Camp');
    $this->landscape = 'country';
    $this->landmark = true;
  }
}
