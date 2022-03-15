<?php
namespace M44\Terrains;

class Hedgehog extends \M44\Models\Obstacle
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['hedgehog']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Hedgehogs');
    $this->number = 5;
    $this->isImpassable = [ARMOR, \ARTILLERY];
    $this->canIgnoreOneFlag = true;

    parent::__construct($row);
  }
}
