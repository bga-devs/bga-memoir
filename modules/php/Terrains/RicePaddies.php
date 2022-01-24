<?php
namespace M44\Terrains;

class RicePaddies extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'price';
    $this->name = clienttranslate('Rice Paddies');
    $this->landscape = 'jungle';
    $this->vegetation = true;
  }
}
