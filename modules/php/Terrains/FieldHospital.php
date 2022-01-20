<?php
namespace M44\Terrains;

class FieldHospital extends \M44\Models\Terrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'phospital';
    $this->name = clienttranslate('Field Hospital');
    $this->landscape = 'jungle';
    $this->landmark = true;
  }
}
