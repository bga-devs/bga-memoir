<?php
namespace M44\Terrains;

class WinterTrenches extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'wtrenches';
    $this->name = clienttranslate('Winter Trenches');
    $this->landscape = 'winter';
    $this->manmade = true;
  }
}
