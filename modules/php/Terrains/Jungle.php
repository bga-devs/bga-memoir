<?php
namespace M44\Terrains;

class Jungle extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'pjungle';
    $this->name = clienttranslate('Jungle');
    $this->landscape = 'jungle';
    $this->vegetation = true;
  }
}
