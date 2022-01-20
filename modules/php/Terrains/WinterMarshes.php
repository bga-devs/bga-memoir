<?php
namespace M44\Terrains;

class WinterMarshes extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'wmarshes';
    $this->name = clienttranslate('Winter Marshes');
    $this->landscape = 'winter';
    $this->vegetation = true;
  }
}
