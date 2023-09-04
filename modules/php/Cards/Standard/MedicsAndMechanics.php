<?php
namespace M44\Cards\Standard;

use M44\Managers\Units;
use M44\Board;
use M44\Dice;
use M44\Core\Game;
use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Helpers\Collection;

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
    $units = $player->getUnits()->filter(function ($unit) {
      return $unit->isWounded() && !$unit->cannotHeal() 
      && !($unit -> getExtraDatas('cannotBeActivatedUntilTurn') >= Globals::getTurn());
    });
    return [
      'i18n' => ['desc'],
      'n' => $player->isMarineCommand() ? 2 : 1,
      'nTitle' => $player->isMarineCommand() ? 2 : 1,
      'nOnTheMove' => 0,
      'desc' => '',
      'sections' => [\INFINITY, \INFINITY, \INFINITY],
      'units' => $units,
    ];
  }

  public function stTargetMedics()
  {
    $args = $this->argsTargetMedics();
    if (empty($args['units'])) {
      Game::get()->nextState('draw');
    } elseif (count($args['units']) == 1) {
      $this->actTargetMedics([$args['units']->first()->getId()]);
    }
  }

  public function actTargetMedics($unitIds)
  {
    $player = $this->getPlayer();
    $args = $this->argsTargetMedics();
    foreach ($unitIds as $unitId) {
      if (!in_array($unitId, $args['units']->getIds())) {
        throw new \feException('This unit cannot be healed. Should not happen');
      }
    }

    $unitHealed = 0;

    foreach ($unitIds as $unitId) {
      // Roll dice corresponding to number of cards
      $unit = Units::get($unitId);
      $nbCard = $player->countAllCards();
      $results = Dice::roll($player, $nbCard, $unit->getPos());

      // Compute number of heal
      $nHeal = $results[DICE_STAR] ?? 0;
      if ($unit->getType() == \INFANTRY) {
        $nHeal += $results[\DICE_INFANTRY] ?? 0;
      }
      if ($unit->getType() == \ARMOR) {
        $nHeal += $results[\DICE_ARMOR] ?? 0;
      }

      if ($nHeal > 0) {
        // Heal and then order the unit
        $nHealed = $unit->heal($nHeal);
        Notifications::healUnit($player, $nHealed, $unit);
        $unit->activate($this);
        Notifications::orderUnits($player, Units::getMany([$unitId]), null);
        $unitHealed++;
      }
    }
    Game::get()->nextState($unitHealed > 0 ? 'move' : 'draw');
  }
}
