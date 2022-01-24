<?php
namespace M44\Terrains;

class Rivermouth extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'pmouth';
    $this->name = clienttranslate('River mouth');
    $this->landscape = 'sand';
    $this->water = true;
  }
}
