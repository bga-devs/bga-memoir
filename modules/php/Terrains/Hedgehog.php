<?php
namespace M44\Terrains;

class Hedgehog extends \M44\Models\Obstacle
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'hedgehog';
    $this->name = clienttranslate('Hedgehog');
    $this->manmade = true;

    $this->impassable = [ARMOR, INFANTRY];
    $this->ignore1Flag = [INFANTRY];
  }
}
