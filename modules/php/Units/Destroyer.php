<?php
namespace M44\Units;

use M44\Managers\Tokens;
use M44\Core\Notifications;

class Destroyer extends BigGun
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = DESTROYER;
    $this->name = clienttranslate('Destroyer');
    $this->number = 12;
    $this->maxUnits = 3;
    $this->attackPower = [3, 3, 2, 2, 1, 1, 1, 1];
    $this->movementRadius = 2;
    $this->movementAndAttackRadius = 2;
    $this->cannotBattleIfMoved = false;
    $this->ignoreCannotBattle = true;
    $this->canIgnoreOneFlag = true;
    $this->maxGrounds = 0;
    $this->maxTarget = 3;
    $this->cannotHeal = true;
    $this->desc = [
      clienttranslate(
        'Place a cross-hair marker when a target is hit on the hex. Until moved or destroyed, zeroed-in unit take fire at +1 on future rounds'
      ),
      clienttranslate('Cross-hair markers are not cumulative'),
    ];
    $this->applyPropertiesModifiers();
  }
}
