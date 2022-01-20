<?php
namespace M44\Terrains;

class Hedgehog extends \M44\Models\Obstacle
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'hedgehog';
    $this->name = clienttranslate('Hedgehog');
    $this->manmade = true;
  }
}
