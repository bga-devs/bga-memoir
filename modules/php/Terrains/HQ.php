<?php
namespace M44\Terrains;

use M44\Managers\Cards;
use M44\Core\Notifications;
use M44\Core\Game;

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
    $this->isBlockingLineOfSight = true;
    parent::__construct($row);
  }

  // TODO: scenario specific / control with owner of HQ
  public function onUnitEntering($unit, $isRetreat, $isTakeGround)
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
      $datas = Game::get()->getAllDatas();
      Notifications::smallRefreshInterface($datas);
      Notifications::smallRefreshHand($unit->getTeam()->getOpponent()->getCommander());
    } elseif ($this->isOriginalOwner($unit) && $this->getExtraDatas('captured') == true) {
      $cards = Cards::draw(1, ['hand', $unit->getPlayer()->getId()]);
      if (is_null($cards)) {
        return;
      }
      Notifications::drawCards($unit->getPlayer(), $cards);
      $this->setExtraDatas('captured', false);
      $datas = Game::get()->getAllDatas();
      Notifications::smallRefreshInterface($datas);
      Notifications::smallRefreshHand($unit->getPlayer());
    }
  }
}
