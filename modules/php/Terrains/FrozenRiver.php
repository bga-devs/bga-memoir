<?php
namespace M44\Terrains;
use M44\Board;
use M44\Dice;
use M44\Core\Game;
use M44\Models\Card;

class FrozenRiver extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['wriver', 'wriverFR', 'wcurved']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Frozen Rivers');
    $this->number = 47;
    $this->desc = [
      \clienttranslate('Frozen Rivers may be crossed, at a risk'),
      \clienttranslate(
        'Moving or retreating onto Frozen Rivers hex, roll 2 Battle dice; for each star rolled, lose 1 figure'
      ),
    ];

    parent::__construct($row);
    $this->avoidIfPossible = true;
  }

  public function onUnitEntering($unit, $isRetreat, $isTakeGround)
  {
    if (Board::isBridgeCell(['x' => $this->x, 'y' => $this->y])) {
      return;
    }

    // FrozenRiver are not triggered with behind ennemy lines (or counter attack after BEL)
    $activationcard = $unit->getActivationOCard();
    if (!$isRetreat && 
    ($activationcard->getType() == CARD_BEHIND_LINES ||
      ($activationcard->getType() == CARD_COUNTER_ATTACK) && 
      $activationcard->getExtraDatas('card')['type'] == CARD_BEHIND_LINES)) {
      return false;
    }

    $player = $unit->getPlayer();
    $attacker = $unit
      ->getTeam()
      ->getOpponent()
      ->getCommander();
    $results = Dice::roll($player, 2, $unit->getPos());

    $hits = $results[\DICE_STAR] ?? 0;
    return Game::get()->damageUnit($unit, $attacker, $hits);
  }
}
