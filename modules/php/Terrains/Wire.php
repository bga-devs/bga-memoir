<?php
namespace M44\Terrains;

class Wire extends \M44\Models\Obstacle
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['wire']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Wire');
    $this->number = 15;
    $this->mustStop = true;
    $this->offense = [INFANTRY => -1];

    parent::__construct($row);
  }

  public function onUnitEntering($unit)
  {
    // TODO : armor remove them
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
