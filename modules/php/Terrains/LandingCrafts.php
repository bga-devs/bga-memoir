<?php
namespace M44\Terrains;

class LandingCrafts extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'barge';
    $this->name = clienttranslate('Landing Crafts');
    $this->transport = true;
    $this->water = true;
  }
}
