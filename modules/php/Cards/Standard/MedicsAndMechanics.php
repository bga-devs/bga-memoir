<?php
namespace M44\Cards\Standard;

use M44\Managers\Units;
use M44\Board;
use M44\Core\Game;
use M44\Core\Globals;
use M44\Core\Notifications;

class MedicsAndMechanics extends \M44\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = \CARD_MEDICS;
    $this->name = clienttranslate('Medics & Mechanics');
    $this->text = [
      clienttranslate('Issue an order to 1 unit that has suffered casualties.'),
      clienttranslate('Roll 1 battle die for each command card you have, including this card.'),
      clienttranslate(
        'For each die matching the unit\'s symbol or a star, 1 lost figure of that unit is returned to duty. A unit may not gain more figures than it originally had.'
      ),
      clienttranslate('If the unit recovers at least 1 figure, it may also be issued an order.'),
    ];
  }

  public function nextStateAfterPlay()
  {
    return 'medics';
  }

  public function argsTargetMedics()
  {
    $player = $this->getPlayer();
    $oUnits = $player->getUnits();
    $units = [];

    foreach ($oUnits as $oUnit) {
      if (!$oUnit->isWounded()) {
        continue;
      }

      $unit = [];
      $unit['x'] = $oUnit->getX();
      $unit['y'] = $oUnit->getY();
      $units[$oUnit->getId()] = $unit;
    }

    return ['units' => $units];
  }

  public function stTargetMedics()
  {
    $args = $this->argsTargetMedics();
    if (count($args['units']) == 0) {
      // TODO: transition
    }
  }

  public function actTargetMedics($unitId)
  {
    $player = $this->getPlayer();
    // check that Ids are ennemy
    $args = $this->argsTargetMedics();

    if (!in_array($unitId, array_keys($args['units']))) {
      throw new \feException('This unit cannot be healed. Should not happen');
    }

    $unit = Units::get($unitId);
    $nbCard = $player->getCards()->count() + 1;

    $results = array_count_values(Game::get()->rollDice($player, $nbCard, $unit->getPos()));
    $heal = 0;
    if ($unit->getType() == \INFANTRY) {
      $heal += $results[\DICE_INFANTRY] ?? 0;
    }

    if ($unit->getType() == \ARMOR) {
      $heal += $results[\DICE_ARMOR] ?? 0;
    }

    $heal += $results[DICE_STAR] ?? 0;

    if ($heal > 0) {
      $healed = $unit->heal($heal);
      Notifications::heal($player, $healed, $unit);
      $unit->activate($this);
      Notifications::orderUnits($player, Units::getMany($unit->getId()), null);
      Game::get()->nextState('move');
    } else {
      Game::get()->nextState('draw');
    }
  }
}
