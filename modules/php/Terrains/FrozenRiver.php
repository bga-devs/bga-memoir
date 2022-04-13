<?php
namespace M44\Terrains;
use M44\Board;
use M44\Dice;
use M44\Core\Game;

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
  }

  public function onUnitEntering($unit, $isRetreat)
  {
    if (Board::isBridgeCell(['x' => $this->x, 'y' => $this->y])) {
      return;
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
