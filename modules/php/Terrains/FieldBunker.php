<?php
namespace M44\Terrains;

class FieldBunker extends \M44\Models\RectTerrain
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = 'casemate';
    $this->name = clienttranslate('Field Bunker');
    $this->landscape = 'country';
    $this->bunker = true;
  }
}
