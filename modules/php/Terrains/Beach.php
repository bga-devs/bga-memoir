<?php
namespace M44\Terrains;

class Beach extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'pbeach';
    $this->name = clienttranslate('Beach');
    $this->landscape = 'sand';
    $this->vegetation = true;
  }
}
