<?php
namespace M44\Terrains;

class Woods extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'woods';
    $this->name = clienttranslate('Woods');
    $this->landscape = 'country';
    $this->vegetation = true;
  }
}
