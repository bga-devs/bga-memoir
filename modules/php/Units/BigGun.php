<?php
namespace M44\Units;

use M44\Managers\Tokens;
use M44\Core\Notifications;

class BigGun extends Artillery
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Big Gun');
    $this->attackPower = [3, 3, 2, 2, 1, 1, 1, 1];
    $this->number = 3;
    $this->maxTarget = 3;
    $this->desc = [
      clienttranslate('Fire at 3, 3, 2, 2, 1, 1, 1, 1'),
      clienttranslate(
        'Place a cross-hair marker when a target is hit on the hex. Until moved or destroyed, zeroed-in unit take fire at +1 on future rounds'
      ),
      clienttranslate('Cross-hair markers are not cumulative'),
    ];
  }

  protected function getTokens($cell = null)
  {
    return Tokens::getOnCoords('target_' . $this->id, $cell);
  }

  public function getAttackModifier($target)
  {
    return $this->getTokens($target)->empty() ? 0 : 1;
  }

  public function afterAttack($coords, $hits, $eliminated)
  {
    if ($hits == 0 || $eliminated || $this->getTokens()->count() >= $this->maxTarget) {
      return;
    }

    if ($this->getTokens($coords)->count() == 0) {
      $token = Tokens::singleCreate([
        'x' => $coords['x'],
        'y' => $coords['y'],
        'location' => 'target_' . $this->id,
        'sprite' => 'target',
        'type' => \TOKEN_TARGET,
      ]);
      Notifications::addToken($token);
    }
  }

  // Called if after an attack a retreat gave a hit
  public function afterAttackRetreatHit($coords, $hits, $eliminated)
  {
    $this->afterAttack($coords, $hits, $eliminated);
  }
}
