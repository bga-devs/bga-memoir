<?php
namespace M44\Terrains;

class Cemetery extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'cemetery';
    $this->name = clienttranslate('Cemetery');
    $this->landscape = 'country';
    $this->landmark = true;
  }
}
