<?php
namespace M44\Terrains;

class Church extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'church';
    $this->name = clienttranslate('Church');
    $this->landscape = 'country';
    $this->landmark = true;
  }
}
