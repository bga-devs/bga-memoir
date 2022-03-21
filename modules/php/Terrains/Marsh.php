<?php
namespace M44\Terrains;
use M44\Board;

class Marsh extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['marshes']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Marshes');
    $this->number = 28;
    $this->desc = [
      \clienttranslate(
        'An Infantry or Armor unit that moves onto a Marsh hex must stop and may not move further on that turn.'
      ),
      clienttranslate('An Infantry or Armor unit moving out may only move onto an adjacent hex.'),
      clienttranslate('Impassable by Artillery'),
      clienttranslate('Infantry moving in or out does not have any combat restrictions'),
      clienttranslate('Armor moving in/out cannot battle'),
      clienttranslate(
        'Armor that makes successfull combat against a unit on a Marsh may Take Ground, but not Armor overrun.'
      ),
      clienttranslate('Do not block line of sight'),
    ];
    $this->isImpassable = [ARTILLERY];
    $this->mustStopWhenEntering = true;
    $this->mustStopWhenLeaving = true;
    $this->enteringCannotBattle = [ARMOR];
    $this->leavingCannotBattle = [ARMOR]; // TODO
    $this->cannotArmorOverrun = true;

    parent::__construct($row);
  }
}
