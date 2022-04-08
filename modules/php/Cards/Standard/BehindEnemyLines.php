<?php
namespace M44\Cards\Standard;
use M44\Managers\Units;
use M44\Core\Game;

class BehindEnemyLines extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_BEHIND_LINES;
    $this->name = clienttranslate('Behind Enemy Lines');
    $this->text = [
      clienttranslate('Issue an order to 1 <INFANTRY> unit.'),
      clienttranslate('Unit may move up to 3 hexes.'),
      clienttranslate('Unit may battle with 1 additional die, then move again up to 3 hexes.'),
      clienttranslate('Terrain movement restrictions are ignored.'),
      clienttranslate('Terrain battle restrictions still apply.'),
      clienttranslate('If you do not command any infantry units, issue an order to 1 unit of your choice.'),
    ];
  }

  public function getArgsOrderUnits()
  {
    $player = $this->getPlayer();
    $units = $player->getUnits();

    // Keep only infantry
    $infantry = $units->filter(function ($unit) {
      return $unit->getType() == \INFANTRY;
    });

    if ($infantry->empty()) {
      // No infantry => 1 unit of your choice
      return [
        'i18n' => ['desc'],
        'n' => 1,
        'nTitle' => 1,
        'desc' => \clienttranslate('(because no infantry units)'),
        'units' => $units->getPositions(),
      ];
    } else {
      return [
        'i18n' => ['desc'],
        'n' => 1,
        'nTitle' => 1,
        'desc' => \clienttranslate('(infantry unit only)'),
        'units' => $infantry->getPositions(),
      ];
    }
  }

  public function getDiceModifier($unit, $cell)
  {
    return 1;
  }

  public function nextStateAfterAttacks()
  {
    return 'moveAgain';
  }

  public function stMoveAgain()
  {
    $units = Units::getActivatedByCard($this);
    $oneActive = false;
    foreach ($units as $unit) {
      $unit->setMoves(0);
      if (!$unit->isEliminated()) {
        $oneActive = true;
      }
    }

    if (!$oneActive) {
      Game::get()->actMoveUnitsDone(false);
    }
  }
}
