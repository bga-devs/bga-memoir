<?php
namespace M44\Terrains;

class PalmForest extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'palmtrees';
    $this->name = clienttranslate('Palm Forest');
    $this->landscape = 'sand';
    $this->vegetation = true;
  }
}
