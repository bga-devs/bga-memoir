<?php
namespace M44\Terrains;

class Barbedwire extends \M44\Models\Obstacle
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'wire';
    $this->name = clienttranslate('Barbed wire');
    $this->manmade = true;
  }
}
