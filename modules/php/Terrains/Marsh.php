<?php
namespace M44\Terrains;
use M44\Board;
use M44\Core\Notifications;

class Marsh extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['marshes', 'wmarshes']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Marshes');
    $this->number = 28;
    $this->desc = [
      clienttranslate('Infantry moving in or out does not have any combat restrictions'),
      clienttranslate('Armor moving in/out cannot battle'),
      clienttranslate(
        'Armor that makes successfull combat against a unit on a Marsh may Take Ground, but not Armor overrun.'
      ),
    ];
    $this->isImpassable = [ARTILLERY];
    $this->mustStopWhenEntering = true;
    $this->mustStopWhenLeaving = true;
    $this->enteringCannotBattle = [ARMOR];
    $this->leavingCannotBattle = [ARMOR];
    $this->cannotArmorOverrun = true;

    parent::__construct($row);
  }

  public function onUnitLeaving($unit, $isRetreat, $cell, $sourceCell = null)
  {
    if (!$isRetreat && $this->leavingCannotBattle($unit)) {
      $unit->setExtraDatas('cannotBattle', true);
    }
  }
  
  public function onUnitEntering($unit, $isRetreat, $isTakeGround)
  {
    if ($isTakeGround && $unit->getType() == ARMOR) {
      Notifications::message(clienttranslate('Armor cannot overrun in marsh'), []);
      $unit->setExtraDatas('cannotBattle', true);
      $unit->setExtraDatas('cannotArmorOverrun', true);
      $unit->disable();
      return;
    }
  }
}
