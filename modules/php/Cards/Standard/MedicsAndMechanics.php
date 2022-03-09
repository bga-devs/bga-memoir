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
      return $unit->isWounded();
    });

    return ['unitIds' => $units->getIds()];
  }

  public function stTargetMedics()
  {
    $args = $this->argsTargetMedics();
    if (empty($args['unitIds'])) {
      Game::get()->nextState('draw');
    } else if(count($args['unitIds']) == 1){
      $this->actTargetMedics($args['unitIds'][0]);
    }
  }

  public function actTargetMedics($unitId)
  {
    $player = $this->getPlayer();
    // check that Ids are ennemy
    $args = $this->argsTargetMedics();
    if (!in_array($unitId, $args['unitIds'])) {
      throw new \feException('This unit cannot be healed. Should not happen');
    }

    // Roll dice corresponding to number of cards
    $unit = Units::get($unitId);
    $nbCard = $player->getCards()->count() + 1;
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
    }

    Game::get()->nextState($nHeal > 0 ? 'move' : 'draw');
  }
}
