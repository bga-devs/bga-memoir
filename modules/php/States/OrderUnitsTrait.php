<?php
namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Units;
use M44\Managers\Tokens;
use M44\Helpers\Utils;
use M44\Board;
use M44\Dice;

trait OrderUnitsTrait
{
  function argsOrderUnits($player = null)
  {
    $player = $player ?? Players::getActive();
    $card = $player->getCardInPlay();
    return $card->getArgsOrderUnits();
  }

  function stOrderUnits($player = null)
  {
    $player = $player ?? Players::getActive();
    $args = $this->argsOrderUnits($player);
    $tmpSection = [0, 0, 0];

    if ($args['units']->count() <= $args['n']) {
      // Do the automatic selection only if sections constraints are ok
      if (isset($args['sections'])) {
        $unitsPerSections = [0, 0, 0];

        // Start assigning unit with only one section, if possible
        foreach ($args['units'] as $unit) {
          if (count($unit['sections']) == 1) {
            $section = $unit['sections'][0];
            if ($unitsPerSections[$section] == $args['sections'][$section]) {
              return;
            } else {
              $unitsPerSections[$section]++;
            }
          }
        }
        // Now tru assigning unit with two sections where it fits
        foreach ($args['units'] as $unit) {
          if (count($unit['sections']) > 1) {
            $section1 = $unit['sections'][0];
            $section2 = $unit['sections'][1];
            if ($unitsPerSections[$section1] < $args['sections'][$section1]) {
              $unitsPerSections[$section1]++;
            } elseif ($unitsPerSections[$section2] < $args['sections'][$section2]) {
              $unitsPerSections[$section2]++;
            } else {
              return;
            }
          }
        }
      }

      $this->actOrderUnits($args['units']->getIds(), [], true);
    }
  }

  function actOrderUnits($unitIds, $onTheMoveIds, $auto = false)
  {
    // Sanity checks
    if (!$auto) {
      $this->checkAction('actOrderUnits');
    }
    $player = Players::getCurrent();
    $args = $this->argsOrderUnits($player);
    if (count($unitIds) > $args['n']) {
      throw new \BgaVisibleSystemException('More units than authorized. Should not happen');
    }
    if (count($onTheMoveIds) > ($args['nOnTheMove'] ?? 0)) {
      throw new \BgaVisibleSystemException('More on the move units than authorized. Should not happen');
    }

    $selectableIds = $args['units']->getIds();
    if (count(array_diff($unitIds, $selectableIds)) != 0) {
      throw new \feException('You selected a unit that cannot be selected');
    }

    if (count(array_diff($onTheMoveIds, $selectableIds)) != 0) {
      throw new \feException('You selected a unit that cannot be selected');
    }

    // TODO : add sanity check for sections !

    // Flag the units as activated by the corresponding card
    $card = $player->getCardInPlay();
    foreach ($unitIds as $unitId) {
      Units::get($unitId)->activate($card);
    }
    foreach ($onTheMoveIds as $unitId) {
      Units::get($unitId)->activate($card, true);
    }

    // Notify
    if (!empty($unitIds) || !empty($onTheMoveIds)) {
      Notifications::orderUnits($player, Units::getMany($unitIds), Units::getMany($onTheMoveIds));
    }

    // Get next state from the card
    $nextState = $card->nextStateAfterOrder($unitIds, $onTheMoveIds);
    $this->nextState($nextState);
  }

  public function actHealUnit($unitId, $nDice = null)
  {
    self::checkAction('actHealUnit');
    $player = Players::getCurrent();

    $unit = Units::get($unitId);
    $moves = $unit->getPossibleMoves();
    Utils::filter($moves, function ($m) {
      return isset($m['type']) && $m['type'] == 'action' && $m['action'] == 'actHealUnit';
    });
    if (count($moves) == 0) {
      throw new \feException('This unit cannot be healed. Should not happen');
    }

    // Roll dice corresponding to number of cards
    if (is_null($nDice)) {
      $nbCard = $player->getCards()->count() + 1;
    } else {
      $nbCard = $nDice;
    }
    $results = Dice::roll($player, $nbCard, $unit->getPos());

    // Compute number of heal
    $nHeal = $results[DICE_STAR] ?? 0;
    if ($unit->getType() == \INFANTRY) {
      $nHeal += $results[\DICE_INFANTRY] ?? 0;
    }

    if ($nHeal > 0) {
      // Heal and then disable the unit all together
      $nHealed = $unit->heal($nHeal);
      Notifications::healUnit($player, $nHealed, $unit);
      $unit->disable();
      // Notifications::disable($unit);
    }

    $this->nextState('moveUnits');
  }

  public function actHealUnitHospital($unitId)
  {
    self::checkAction('actHealUnitHospital');

    $this->actHealUnit($unitId, 6);
  }

  public function actExitUnit($unitId)
  {
    self::checkAction('actExitUnit');
    $player = Players::getCurrent();
    $args = $this->argsMoveUnits($player, false);
    if (!\array_key_exists($unitId, $args['units'])) {
      throw new \BgaVisibleSystemException('You cannot move this unit. Should not happen');
    }

    $unit = Units::get($unitId);

    $unit->setNUnits(0);
    // $pos = $unit->getPos();
    $team = $unit->getTeam();
    Notifications::exitUnit($player, $unit);

    Board::removeUnit($unit);
    $medals = $team->addExitMedals($unit);

    Tokens::removeTargets($unit->getPos());
    Tokens::removeCamouflage($unit->getPos());
    if (Teams::checkVictory()) {
      return;
    }

    $this->nextState('moveUnits');
  }
}
