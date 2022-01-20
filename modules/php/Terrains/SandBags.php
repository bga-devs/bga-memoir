<?php
namespace M44\Terrains;

class SandBags extends \M44\Models\Obstacle
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'sand';
    $this->name = clienttranslate('Sand Bags');
    $this->manmade = true;
  }
}
