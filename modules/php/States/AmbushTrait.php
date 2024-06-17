<?php

namespace M44\States;

use M44\Core\Globals;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Cards;
use M44\Managers\Units;
use M44\Core\Notifications;
use M44\Helpers\Utils;
use M44\Board;

use M44\Dice;
use M44\Models\Terrain;

trait AmbushTrait
{
  function argsOpponentAmbush()
  {
    $player = Players::getActive();
    $attack = $this->getCurrentAttack(false);
    // add terrain cannot battle condition 93490
    $ambushedUnit = Units::get($attack['oppUnitId']);
    $terrains = Board::getTerrainsInCell($ambushedUnit->getPos());
    $cannotBattleFromTerrain = false;
    foreach ($terrains as $t) {
      if (!$cannotBattleFromTerrain) {
        $cannotBattleFromTerrain = $t->cannotBattle($ambushedUnit, 0);
      }
    }

    $canAttack = ($ambushedUnit->canTarget(Units::get($attack['unitId'])))
      && !($ambushedUnit->getExtraDatas('cannotBeActivatedUntilTurn') >= Globals::getTurn())
      && !$cannotBattleFromTerrain
      && !empty($ambushedUnit->attackPower());

    $cards = $canAttack
      ? $player
      ->getCards()
      ->filter(function ($card) {
        return $card->getType() == CARD_AMBUSH;
      })
      ->getIds()
      : [];

    return [
      'canAttack' => $canAttack,
      'currentAttack' => $attack,
      '_private' => ['active' => ['cards' => $cards]],
    ];
  }

  function stAmbush()
  {
    // check if ambush viable else pass
    $attack = $this->getCurrentAttack();
    if ($attack['distance'] != 1) {
      $this->actPassAmbush(true);
      return;
    }

    // If ALL the ambush cards are in the discard => pass
    $locations = array_values(
      array_unique(
        Cards::getByType(\CARD_AMBUSH)
          ->map(function ($card) {
            return $card->getLocation();
          })
          ->toArray()
      )
    );
    if (count($locations) == 1 && $locations[0] == 'discard') {
      $this->actPassAmbush(true);
      return;
    }

    // If the player can't ambush current attacking unit due to attack restriction => pass (sniper on Armor)
    $args = $this->argsOpponentAmbush();
    if (!$args['canAttack']) {
      Notifications::message(clienttranslate('Unit attacked can not battle nor ambush'));
      $this->actPassAmbush(true);
      return;
    }

    // If player has autoskip and no ambush in hand => pass
    $player = Players::getActive();
    if ($player->getPref(\OPTION_AUTO_PASS_ATTACK_REACT) == \OPTION_AUTO_ON) {
      if (empty($args['_private']['active']['cards'])) {
        $this->actPassAmbush(true);
      }
    } else {
      $this->giveExtraTime($player->getId(), 30);
    }
  }

  function actAmbush()
  {
    // Sanity check
    $this->checkAction('actAmbush');
    Globals::incActionCount();
    $player = Players::getCurrent();
    $args = $this->argsOpponentAmbush();
    if (count($args['_private']['active']['cards']) == 0) {
      throw new \BgaVisibleSystemException('You cannot play this card. Should not happen.');
    }
    $cardId = array_pop($args['_private']['active']['cards']);

    // inPlay card
    $card = Cards::play($player, $cardId, null);
    Notifications::ambush($player, $card);

    $stack = Globals::getAttackStack();
    $stack[count($stack) - 1]['ambush'] = true;
    Globals::setAttackStack($stack);

    $currentAttack = $this->getCurrentAttack();
    $unit = $currentAttack['oppUnit'];
    $ambushedUnit = $currentAttack['unit'];

    // get diceNumber + modifiers
    $cells = $unit->getTargetableUnits();
    $k = Utils::searchCell($cells, $ambushedUnit->getX(), $ambushedUnit->getY());
    if ($k === false) {
      throw new \BgaVisibleSystemException(clienttranslate('You cannot ambush the unit, not enough attack power'));
    }
    $target = $cells[$k];

    // Launch dice
    $results = Dice::roll($player, $target['dice'], $ambushedUnit->getPos());

    // if tiger is ambushed double roll check for damages
    if ($ambushedUnit->getNumber() == '16') {
      $hits = $this->calculateHits($unit, $ambushedUnit, null, $results);
      // Count all FLAGS and if Tiger would be blocked to retreat to add to hits for second roll 
      // (remove all related flags from 1st in this case results)
      if (isset($results[DICE_FLAG])) {
        $hitflag = $this->calculateTigerFlagsHits($currentAttack, $results, $ambushedUnit);
        $hits += $hitflag;
        $results[DICE_FLAG] -= $hitflag;
      }
      // Second roll dice if hits >0 (armor and grenade)
      if ($hits > 0) {
        Notifications::message(clienttranslate('Tiger second roll'), []);
        $results2 = Dice::roll($player, $hits, $ambushedUnit->getPos());
        $hits2 = $this->calculateHitsTiger2ndRoll($results2);
        $eliminated = $this->damageUnit($ambushedUnit, $player, $hits2);
        if (Teams::checkVictory()) {
          return;
        }
      } else {
        $eliminated = false;
      }
    } else { // standard unit else than a tiger
      // $hits = $ambushedUnit->getHits($results);
      $hits = $this->calculateHits($unit, $ambushedUnit, null, $results);
      $eliminated = $this->damageUnit($ambushedUnit, $player, $hits, false, true);

      if (Teams::checkVictory()) {
        return;
      }
    }


    $attack = [
      'pId' => $player->getId(),
      'unitId' => $unit->getId(),
      'x' => $unit->getX(),
      'y' => $unit->getY(),
      'oppUnitId' => $ambushedUnit->getId(),
      'nDice' => $target['dice'],
      'distance' => $target['d'],
    ];

    // Handle retreat
    if (isset($results[DICE_FLAG]) && !$eliminated) {
      // add condition if artillery ambushed activated by Bombard and retreat, add 1 fight in order to be at max attack
      if (
        $ambushedUnit->getType() == \ARTILLERY
        && $ambushedUnit->getActivationOCard()->isType(CARD_ARTILLERY_BOMBARD)
      ) {
        $ambushedUnit->incFights(1);
      }
      $this->initRetreat($attack, $results);
      $this->nextState('retreat', $ambushedUnit->getPlayer());
    } else {
      $this->nextState('pass', $ambushedUnit->getPlayer());
    }
  }

  function actPassAmbush($silent = false)
  {
    if (!$silent) {
      // Sanity checks
      self::checkAction('actPassAmbush');
      Globals::incActionCount();
      Notifications::message(clienttranslate('${player_name} does not react to the attack'), [
        'player' => Players::getActive(),
      ]);
    }

    $attack = $this->getCurrentAttack();
    $this->nextState('pass', $attack['pId']);
  }
}
