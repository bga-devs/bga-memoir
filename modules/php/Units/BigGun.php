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
  }

  public function getAttackModifier($target)
  {
    return (int) Tokens::getOnCoords('target', $target)->count() != 0;
  }

  public function afterAttack($coords, $hits, $eliminated)
  {
    if ($hits == 0 || $eliminated) {
      return;
    }
    if (Tokens::getOnCoords('target', null)->count() >= $this->maxTarget) {
      return;
    }

    if (Tokens::getOnCoords('target', $coords)->count() == 0) {
      $token = [
        [
          'x' => $coords['x'],
          'y' => $coords['y'],
          'location' => 'target',
          'sprite' => 'target',
          'type' => 0,
        ],
      ];
      $created = Tokens::create($token);
      Notifications::addToken(Tokens::get($created));
    }
  }

  // Called if after an attack a retreat gave a hit
  public function afterAttackRetreatHit($coords, $hits, $eliminated)
  {
    $this->afterAttack($coords, $hits, $eliminated);
  }
}
