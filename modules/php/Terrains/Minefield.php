<?php
namespace M44\Terrains;
use M44\Board;
use M44\Dice;
use M44\Core\Game;
use M44\Core\Notifications;
use M44\Managers\Medals;
use M44\Managers\Terrains;
use M44\Managers\Teams;
use M44\Helpers\Log;

class Minefield extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return false;
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('Minefields');
    $this->number = 29;
    $this->desc = [];
    $this->mustStopWhenEntering = true;

    parent::__construct($row);
  }

  public function onUnitEntering($unit, $isRetreat, $isTakeGround)
  {
    // A bit counter-intuitive but the side indicated by editor is the one that is affected by mines
    if ($isRetreat || !$this->isOriginalOwner($unit)) {
      return false;
    }
    // mines are not triggered with behind ennemy lines
    if ($unit->getActivationOCard()->getType() == CARD_BEHIND_LINES && $unit->getMoves() < 3) {
      return false;
    }

    if ($unit->mustSweep() && !$unit->isOnTheMove()) {
      if (
        $unit->getMoves() <= $unit->getMovementAndAttackRadius() ||
        ($unit->getActivationOCard()->getType() == \CARD_BEHIND_LINES && $unit->getMoves() == 3)
      ) {
        // Sweep the mine
        Notifications::message(clienttranslate('Combat engineer sweeps the mine instead of battling'), []);
        $this->removeFromBoard();
        $unit->disable();
        return;
      }
    }

    $isHidden = $this->tile == 'mineX';
    $value = $this->getExtraDatas('value');
    if ($isHidden) {
      // Reveal the mine
      Notifications::revealMinefield($unit->getPlayer(), $this->id, $this->getPos(), $value);
      $this->setTile('mine' . $value);
      Log::checkpoint(); // Make undo invalid
    }

    if ($value == 0) {
      $this->removeFromBoard();

      if ($this->getExtraDatas('decoyMedal')) {
        $medals = Medals::addDecoyMedals($unit->getTeamId(), $this);
        Notifications::scoreMedals($unit->getTeamId(), $medals, $unit->getPos());
        Terrains::removeDecoyMedals();
        return Teams::checkVictory();
      }
    } else {
      $player = $unit->getPlayer();
      $results = Dice::roll($player, $value, $unit->getPos());
      $attacker = $unit
        ->getTeam()
        ->getOpponent()
        ->getCommander();
      $hits = Game::get()->calculateHits(null, $unit, null, $results);
      return Game::get()->damageUnit($unit, $attacker, $hits);
    }
  }

  public function getPossibleAttackActions($unit)
  {
    if ($unit->mustSweep()) {
      return [
        [
          'desc' => \clienttranslate('Sweep mine'),
          'action' => 'actSweepMine',
        ],
      ];
    } else {
      return [];
    }
  }
}
