<?php
namespace M44\Terrains;
use M44\Board;

class RicePaddie extends Marsh
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['price']);
  }

  public function __construct($row)
  {
    parent::__construct($row);

    $this->name = clienttranslate('Rice Paddies');
    $this->number = 60;
    $this->desc = [
      clienttranslate('Infantry moving in or out does not have any combat restrictions'),
      clienttranslate('Armor moving in/out cannot battle'),
      clienttranslate(
        'Armor that makes successfull combat against a unit on Rice paddies may Take Ground, but not Armor overrun.'
      ),
    ];
  }
}
