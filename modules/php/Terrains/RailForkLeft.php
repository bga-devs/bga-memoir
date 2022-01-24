<?php
namespace M44\Terrains;

class RailForkLeft extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'railFL';
    $this->name = clienttranslate('Rail Fork - Left');
    $this->landscape = 'country';
    $this->rail = true;
  }
}
