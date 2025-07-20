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
use M44\Scenario;

trait OrderUnitsTrait
{
  function argsOrderUnits($player = null)
  {
    $player = $player ?? Players::getActive();
    $card = $player->getCardInPlay();
    $args = $card->getArgsOrderUnits();
    $args['actionCount'] = Globals::getActionCount();
    return $args;
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
      Globals::incActionCount();
    }
    $player = Players::getCurrent();
    $args = $this->argsOrderUnits($player);

    // Case train if contain LOCO or WAGON add all LOCO and WAGON in $unitids and count them as a whole unit
    /* $units = $this->getPlayer()
      ->getTeam()
      ->getOpponent()
      ->getUnits()*/
    $train = $player->getTeam()->getUnits()->filter(function ($unit) {
      return in_array($unit->getType(), [LOCOMOTIVE, WAGON]) && !$unit->isEliminated();
    });
    $trainIds = $train->getIds();

    $trainSelectedIds = array_intersect($unitIds, $trainIds);
    $countTrainModifier = count($train) > 1 && count($trainSelectedIds) > 0 ? 1 : 0;

    $selectableIds = $args['units']->getIds();
    $notSelectedTrainIds = [];

    // If selected only one we add the others loco or wagons to selected and selectable units and train is 2
    if (count($trainIds) > 1) {
      $notSelectedTrainIds = array_diff($trainIds, $trainSelectedIds);
    }

    if (count($notSelectedTrainIds) == 1) {
      $unitIds[] = implode($notSelectedTrainIds);
      $selectableIds[] = implode($notSelectedTrainIds);

      // TO DO : extend to Breakthrough onTheMove
    }

    if (count($unitIds) - $countTrainModifier > $args['n']) {
      throw new \BgaVisibleSystemException('More units than authorized. Should not happen');
    }
    if (count($onTheMoveIds) - $countTrainModifier > ($args['nOnTheMove'] ?? 0)) {
      throw new \BgaVisibleSystemException('More on the move units than authorized. Should not happen');
    }

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
    Globals::incActionCount();
    $player = Players::getCurrent();

    $unit = Units::get($unitId);
    $moves = $unit->getPossibleMoves();
    Utils::filter($moves, function ($m) {
      return isset($m['type']) &&
        $m['type'] == 'action' &&
        in_array($m['action'], ['actHealUnit', 'actHealUnitHospital']);
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
    Globals::incActionCount();
    $player = Players::getCurrent();
    $args = $this->argsMoveUnits($player, false);
    $unit = $args['units'][$unitId] ?? null;
    if (is_null($unit)) {
      throw new \BgaVisibleSystemException('You cannot move this unit. Should not happen');
    }

    $unit = Units::getSingle($unitId);
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

    $desertMove = $this->gamestate->state()['name'] == 'desertMove' ? true : false;
    if ($desertMove) {
      // we need to close current attack as the unit exited
      $this->closeCurrentAttack();
      return;
    }
    $this->nextState('moveUnits');
  }

  public function argsArmorBreakthroughDeploy()
  {
    //  s'inspirer de baseline reserve deploy
    
    $scenario = Globals::getScenario();
    $mode = Scenario::getMode();
    $sidePlayer1 = isset($scenario['game_info']['side_player1']) ? $scenario['game_info']['side_player1'] : 'AXIS';
    $dim = Board::$dimensions[$mode];
    $cells = Board::getListOfCells();

    $oppPlayer = Players::getActive()->getTeam()->getOpponent()->getCommander();
    $yBackLine = $sidePlayer1 == $oppPlayer->getTeam()->getId() ? 0 : $dim['y']-1;

    // filter cells on player backline and no unit on cells nor impassable terrains
    $units = $oppPlayer->getTeam()->getUnits()->toArray(); 
    $unit = Units::getInstance('tank','');
    
    $cells_unit_deployement = array_filter($cells, function ($c) use ($yBackLine, $unit) {
      return $c['y'] == $yBackLine 
      && is_null(Board::getUnitInCell($c))
      && !Board::isImpassableCell($c, $unit);
    });

    // filter cells based on section from activation card
    $player = Players::getActive();
    $card = $player->getCardInPlay();
    $team = $player->getTeam();
    $args = $card->getArgsOrderUnits();
    if (isset($args['sections']) 
    || $card->isType(CARD_INFANTRY_ASSAULT)
    || ($card->isType(CARD_COUNTER_ATTACK) && $card->getExtraDatas('copiedCardType') == \CARD_INFANTRY_ASSAULT)) {
      if (isset($args['sections'])) {
        $argsSections = $args['sections'];
      } elseif ($card->isType(CARD_INFANTRY_ASSAULT)
      || ($card->isType(CARD_COUNTER_ATTACK) && $card->getExtraDatas('copiedCardType') == \CARD_INFANTRY_ASSAULT)) {
        $argsSections = [0, 0, 0];
        $activatedSection = (int) $args['section'];
        $argsSections[$activatedSection] = INFINITY;
      } 
      // filter only starting cells in activated section
      $cells_unit_deployement2 = array_filter($cells_unit_deployement, function ($c) use ($argsSections, $team) {
        return $argsSections[Board::getCellSections($team->getId(), $c)[0]] != 0;
      });
      return $cells_unit_deployement2;
    }
    
    return $cells_unit_deployement;
  }
  
  public function actArmorBreakthroughDeploy($x, $y)
  {
    // Sanity checks
    self::checkAction('actArmorBreakthroughDeploy');
    $args = $this->argsArmorBreakthroughDeploy();
    $k = Utils::searchCell($args, $x, $y);
    if ($k === false) {
      throw new \BgaVisibleSystemException('You cannot performed reinforcement here. Should not happen');
    }

    // get unit name and badge from scenario
    $player = Players::getActive();
    $teamId = $player->getTeam()->getId();
    $options = Scenario::getOptions()['armor_breakthrough_rules'][$teamId];
    $unit = Units::addInCell($options, ['x' => $x, 'y' => $y]);
    Board::addUnit($unit);
    Globals::incReinforcementUnits(1);
    // this unit cannot perform Armor Overrun
    $unit->setExtraDatas('cannotArmorOverrun', true);
    $unit->setExtraDatas('stopped', true);
    $unit->ableTakeGround();
    // if deployed on terrain with battle restriction (eg. forest or village) :
    $cell = $unit->getPos();
    if (Board::enteringCannotBattleCell($cell, $unit)) {
      $unit->setExtraDatas('cannotBattle', true);
    }
    // max nbr of unit to be deployed from the card rule
    $cardInPlay = $player->getCardInPlay();
    $args = $cardInPlay->getArgsOrderUnits();
    $nCardInPlay = $args['n'] ?? 0;
    // activate by default
    $unit->activate($cardInPlay);
    // for specific scenario 4090, axis cannot exit (escape from exit markers)
    if (isset($options['behavior']) && $options['behavior'] == 'CANNOT_EXIT' ) {
      $unit->setExtraDatas('cannotExit', true);
    }


    // Notify added unit
    Notifications::ArmorBreakthroughDeployement($player, $unit);

    
  // TODO tant qu'on a pas deployé nbr_units unité et au maxi n units de la carte jouée => armor_BreakThrough
    if (Globals::getReinforcementUnits() >= $options['nbr_units']
      || Globals::getReinforcementUnits() >= $nCardInPlay) {
      Globals::setReinforcementUnits(0);
      $armorBreakThroughDone = Globals::getArmorBreakthroughDone();
      $armorBreakThroughDone[$teamId] = true;
      Globals::setArmorBreakthroughDone($armorBreakThroughDone);
      $this->gamestate->jumpToState(ST_ORDER_UNITS);
    } else {
      $this->gamestate->jumpToState(ST_ARMOR_BREAKTHROUGH);
    }
  }
}
