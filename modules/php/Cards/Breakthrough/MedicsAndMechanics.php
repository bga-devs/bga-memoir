<?php
namespace M44\Cards\Breakthrough;

class MedicsAndMechanics extends \M44\Cards\Standard\MedicsAndMechanics
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->text = [
      clienttranslate('Roll 4 battle dice.'),
      clienttranslate(
        'For each die matching a unit\'s symbol, you may return 1 lost figure to duty with that unit. For each star, you may return 1 lost figure to duty with a unit of your choice'
      ),
      clienttranslate(' unit may not gain more figures than it originally had.'),
      clienttranslate('If the unit recovers at least 1 figure, it may also be issued an order.'),
    ];

    // TODO
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
    // check that Ids are ennemy
    // throw new \feException(print_r($unitIds));
    $args = $this->argsTargetMedics();
    foreach ($unitIds as $unitId) {
      if (!in_array($unitId, $args['units']->getIds())) {
        throw new \feException('This unit cannot be healed. Should not happen');
      }
    }

    foreach ($unitIds as $unitId) {
      // Roll dice corresponding to number of cards
      $unit = Units::get($unitId);
      $nbCard = $player->countAllCards();
      $results = Dice::roll($player, $nbCard, $unit->getPos());
      $unitHealed = 0;

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
