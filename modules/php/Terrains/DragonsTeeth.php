<?php
namespace M44\Terrains;

class DragonsTeeth extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'dragonteeth';
    $this->name = clienttranslate("Dragon's Teeth");
    $this->landscape = 'winter';
    $this->block = true;
  }
}
