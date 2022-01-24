<?php
namespace M44\Terrains;

class WinterChurch extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'wchurch';
    $this->name = clienttranslate('Winter Church');
    $this->landscape = 'winter';
    $this->landmark = true;
  }
}
