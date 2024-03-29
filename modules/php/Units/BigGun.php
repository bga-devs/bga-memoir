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
      clienttranslate(
        'Place a cross-hair marker when a target is hit on the hex. Until moved or destroyed, zeroed-in unit take fire at +1 on future rounds'
      ),
      clienttranslate('Cross-hair markers are not cumulative'),
    ];
    $this->applyPropertiesModifiers();
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

  // 97993 Remove TOKEN TARGET if this Big Gun move also apply to destroyer by extension
  public function moveTo($cell)
  {
    $this->setX($cell['x']);
    $this->setY($cell['y']);

    // TO DO REMOVE TOKEN TARGET ASSOCIATED TO THIS
    $tokens = $this->getTargetTokens();
    foreach ($tokens as $t) {
      Tokens::DB()->delete($t['id']);
      Notifications::removeToken($t);
    }
  }

  public function getTargetTokens() {
    $tokens = Tokens::getSelectQuery()
      ->where('type', \TOKEN_TARGET)
      ->where('token_location', 'target_' . $this->id)
      ->get();
    return $tokens;
  }

}
