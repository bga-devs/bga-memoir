<?php
namespace M44\Terrains;

use M44\Managers\Cards;
use M44\Core\Notifications;

class HQ extends \M44\Models\Terrain
{
  public static function isTileOfType($hex)
  {
    return in_array($hex['name'], ['dcamp', 'pheadquarter']);
  }

  public function __construct($row)
  {
    $this->name = clienttranslate('HQ & Supply tents');
    $this->number = 56;

    $this->desc = [
      clienttranslate('No movement restriction'),
      clienttranslate('No combat restriction'),
      clienttranslate('Block line of sight'),
    ];

    $this->isBlockingLineOfSight = true;
    parent::__construct($row);
  }

  // TODO: scenario specific / control with owner of HQ
  public function onUnitEntering($unit, $isRetreat)
  {
    // if capture done by ennemy
    if (!$this->isOriginalOwner($unit) && $this->getExtraDatas('captured') !== true) {
      // remove one random card
      $card = $unit
        ->getTeam()
        ->getOpponent()
        ->getCommander()
        ->getCards()
        ->rand();
      Cards::discard($card);
      $this->setExtraDatas('captured', true);
      Notifications::discardHQCapture(
        $unit
          ->getTeam()
          ->getOpponent()
          ->getCommander(),
        $card
      );
    } elseif ($this->isOriginalOwner($unit) && $this->getExtraDatas('captured') == true) {
      $cards = Cards::pickForLocation(1, 'deck', ['hand', $unit->getPlayer()->getId()]);
      if (is_null($cards)) {
        return;
      }
      Notifications::drawCards($unit->getPlayer(), $cards);
      $this->setExtraDatas('captured', false);
    }
  }
}
