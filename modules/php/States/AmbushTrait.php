<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Cards;
use M44\Managers\Units;
use M44\Core\Notifications;
use M44\Helpers\Utils;
use M44\Dice;

trait AmbushTrait
{
  function argsOpponentAmbush()
  {
    $player = Players::getActive();
    $cards = $player->getCards();
    $cards = $cards
      ->filter(function ($card) {
        return $card->getType() == CARD_AMBUSH;
      })
      ->getIds();
    return [
      'currentAttack' => $this->getCurrentAttack(false),
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

    // If the ambush card is in the discard => pass
    $card = Cards::getByType(\CARD_AMBUSH)->first();
    if($card->getLocation() == 'discard'){
      $this->actPassAmbush(true);
      return;
    }


    // If player has autoskip and no ambush in hand => pass
    $player = Players::getActive();
    if ($player->getPref(\OPTION_AUTO_PASS_ATTACK_REACT) == \OPTION_AUTO_ON) {
      $args = $this->argsOpponentAmbush();
      if (empty($args['_private']['active']['cards'])) {
        $this->actPassAmbush(true);
      }
    }
  }

  function actAmbush()
  {
    // Sanity check
    $this->checkAction('actAmbush');
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
      throw new \BgaVisibleSystemException('Issue in ambush. Should not happen');
    }
    $target = $cells[$k];

    // Launch dice
    $results = Dice::roll($player, $target['dice'], $ambushedUnit->getPos());

    // $hits = $ambushedUnit->getHits($results);
    $hits = $this->calculateHits($unit, $ambushedUnit, null, $results);
    $eliminated = $this->damageUnit($ambushedUnit, $hits, false, true);

    if (Teams::checkVictory()) {
      return;
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
      Notifications::message(clienttranslate('${player_name} does not react to the attack'), [
        'player' => Players::getActive(),
      ]);
    }

    $attack = $this->getCurrentAttack();
    $this->nextState('pass', $attack['pId']);
  }
}
