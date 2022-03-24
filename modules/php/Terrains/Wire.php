<?php
namespace M44\Terrains;
use M44\Core\Game;
use M44\Core\Notifications;

class Wire extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['wire']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Wire');
    $this->number = 15;
    $this->mustStopWhenEntering = true;
    $this->offense = [INFANTRY => -1];

    parent::__construct($row);
  }

  public function onUnitEntering($unit, $isRetreat)
  {
    if ($unit->getType() == ARMOR && !$isRetreat) {
      $this->removeFromBoard();
      Notifications::message(\clienttranslate('Wire is removed by the Tank'), []);
      $unit->setMoves($unit->getMovementRadius());
      Game::get()->nextState('moveUnits');
      return true;
    }
  }

  public function onAfterAttack($unit)
  {
    if ($unit->getType() == ARMOR) {
      $this->removeFromBoard();
      Notifications::message(\clienttranslate('Wire is removed by the Tank\'s attack'), []);
    }
  }

  public function getPossibleAttackActions($unit)
  {
    if ($unit->getType() == \INFANTRY) {
      return [
        [
          'desc' => \clienttranslate('Remove Wire'),
          'action' => 'actRemoveWire',
        ],
      ];
    } else {
      return [];
    }
  }
}
