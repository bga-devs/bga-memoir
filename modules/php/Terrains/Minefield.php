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
use M44\States\AttackUnitsTrait;
use M44\Models\Card;

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
    // Note from Scenario Editor 1.2 that created confusion previously :
    /*Important note: 
     * "Only Allies" means that mines are placed by Allies (original ) and will hit Axis.
     * "Only Axis" means that mines are placed by Axis and will hit Allies.
     * "No/Any Side" does not have owner and will hit any side that enters minefield.
     * Notice that this last option is not supported by official M44 rules but can be beneficial for custom scenarios. 
     * Make sure you tell players about Minefields in 'Special rules' section to avoid confusion!
     */
    if ($isRetreat || $this->isOriginalOwner($unit)) {
      return false;
    }
    // mines are not triggered with behind ennemy lines (and counter attack BEL)
    $activationcard = $unit->getActivationOCard();
    if (($activationcard->getType() == CARD_BEHIND_LINES ||
      ($activationcard->getType() == CARD_COUNTER_ATTACK) && 
      $activationcard->getExtraDatas('copiedCardType') == \CARD_BEHIND_LINES)
      && $unit->getMoves() < 3) {
      return false;
    }
    
    if ($unit->mustSweep() && !$unit->isOnTheMove() && !$isTakeGround) {
      if (
        ($unit->getMoves() <= $unit->getMovementAndAttackRadius() ||
          (($activationcard->getType() == CARD_BEHIND_LINES ||
            ($activationcard->getType() == CARD_COUNTER_ATTACK) && 
            $activationcard->getExtraDatas('copiedCardType') == \CARD_BEHIND_LINES)) && $unit->getMoves() == 3)
            ||
            (($activationcard->getType() == CARD_INFANTRY_ASSAULT ||
            ($activationcard->getType() == CARD_COUNTER_ATTACK) && 
            $activationcard->getExtraDatas('copiedCardType') == CARD_INFANTRY_ASSAULT) && $unit->getMoves() == 2)
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
      // case tiger on mine - double roll check for damages
      if ($unit->getNumber() == '16') {
        $hits = Game::get()->calculateHits(null, $unit, null, $results);
        // Second roll dice if hits >0 (armor and grenade)
        if ($hits > 0) {
          Notifications::message(clienttranslate('Tiger second roll'), []);
          $results2 = Dice::roll($player, $hits, $unit->getPos());
          $hits2 = AttackUnitsTrait::calculateHitsTiger2ndRoll($results2);
          return Game::get()->damageUnit($unit, $attacker, $hits2);
        }
      } else { // not a tiger - standard units
      $hits = Game::get()->calculateHits(null, $unit, null, $results);
      return Game::get()->damageUnit($unit, $attacker, $hits);
      }
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
