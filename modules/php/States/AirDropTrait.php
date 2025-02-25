<?php

namespace M44\States;

use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Managers\Cards;
use M44\Managers\Units;
use M44\Helpers\Utils;
use M44\Board;
use M44\Scenario;

trait AirDropTrait
{
  public function argsAirDrop()
  {
    $options = Scenario::getOptions()['airdrop'];
    $cells = Board::getListOfCells();
    $dropcenter = isset($options['option2']) ? $options['option2'] == 'TARGET_DROP_CENTER' : false;

    if (isset($options['behavior']) && isset($options['nbr_drops'])) {
      return [
        'nb' => $options['nbr_units'],
        'cells' => $cells,
        'actionCount' => Globals::getActionCount(),
        'behavior' => $options['behavior'],
        'nb_drops' => $options['nbr_drops'],
        'titleSuffix' => ($dropcenter ? 'dropcentertarget' : ''),
        'targetcenter' => $options['center'],
      ];
    } else {
      return [
        'nb' => $options['nbr_units'],
        'cells' => $cells,
        'actionCount' => Globals::getActionCount(),
        'nb_drops' => 1,
        'titleSuffix' => ($dropcenter ? 'dropcentertarget' : ''),
        'targetcenter' => $options['center'],
      ];
    }
  }

  public function actAirDrop($x, $y)
  {
    // Sanity checks
    self::checkAction('actAirDrop');
    Globals::incActionCount();
    $args = $this->argsAirDrop();
    $k = Utils::searchCell($args['cells'], $x, $y);
    if ($k === false) {
      throw new \BgaVisibleSystemException('You cannot air drop here. Should not happen');
    }

    // Simulate random walks
    $player = Players::getCurrent();
    $options = Scenario::getOptions()['airdrop'];
    Globals::incAirDrops();
    $dropNumber = Globals::getAirDrops();
    $maxi = ($args['nb_drops'] == 1) ?  $args['nb'] : $args['nb'][$dropNumber - 1];


    for ($i = 0; $i < $maxi; $i++) {
      $pos = Board::randomWalk(['x' => $x, 'y' => $y], $options['range']);
      $unit = Units::addInCell($options['unit'], $pos);
      if (
        isset($options['unit']['behavior'])
        && $options['unit']['behavior'] == 'CANNOT_BE_ACTIVATED_TILL_TURN'
      ) {
        //$unit -> setExtraDatas('cannotBeActivated', true);
        $unit->setExtraDatas('cannotBeActivatedUntilTurn', $options['unit']['turn']);
      }

      $fails = 0;
      if (is_null($pos) || Board::isImpassableCell($pos, $unit) || Board::getUnitInCell($pos) !== null) {
        // Unsafed landing => remove unit
        Units::remove($unit->getId());
        $fails++;
      } else {
        // Notify about it
        Board::addUnit($unit);
        Notifications::airDrop($player, $unit);
      }

      if ($fails > 0) {
        Notifications::message(\clienttranslate('${player_name} unsuccessfully air drop ${n} unit(s)'), [
          'player' => $player,
          'n' => $fails,
        ]);
      }
    }
    if (Globals::getAirDrops() >= $args['nb_drops']) {
      $this->gamestate->jumpToState(ST_PREPARE_TURN);
      Globals::setAirDrops(0);
    } else {
      $this->gamestate->jumpToState(ST_AIR_DROP);
    }
  }

  ///////////////////////////////////////////////////////////////
  //    _    _         _                   ____  
  //   / \  (_)_ __ __| |_ __ ___  _ __   |___ \ 
  //  / _ \ | | '__/ _` | '__/ _ \| '_ \    __) |
  // / ___ \| | | | (_| | | | (_) | |_) |  / __/ 
  ///_/   \_\_|_|  \__,_|_|  \___/| .__/  |_____|
  //                              |_|
  ///////////////////////////////////////////////////////////////


  public function argsAirDrop2()
  {
    $options = Scenario::getOptions()['airdrop2'];
    $cells = Board::getListOfCells();
    $dropcenter = isset($options['option2']) ? $options['option2'] == 'TARGET_DROP_CENTER' : false;

    if (isset($options['behavior']) && isset($options['nbr_drops'])) {
      return [
        'nb' => $options['nbr_units'],
        'cells' => $cells,
        'actionCount' => Globals::getActionCount(),
        'behavior' => $options['behavior'],
        'nb_drops' => $options['nbr_drops'],
        'titleSuffix' => ($dropcenter ? 'dropcentertarget' : ''),
        'targetcenter' => $options['center'],
      ];
    } else {
      return [
        'nb' => $options['nbr_units'],
        'cells' => $cells,
        'actionCount' => Globals::getActionCount(),
        'nb_drops' => 1,
        'titleSuffix' => ($dropcenter ? 'dropcentertarget' : ''),
        'targetcenter' => $options['center'],
      ];
    }
  }

  public function actAirDrop2($x, $y)
  {
    // Sanity checks
    self::checkAction('actAirDrop2');
    Globals::incActionCount();
    $args = $this->argsAirDrop2();
    $k = Utils::searchCell($args['cells'], $x, $y);
    if ($k === false) {
      throw new \BgaVisibleSystemException('You cannot air drop here. Should not happen');
    }

    // Simulate random walks
    $player = Players::getCurrent();
    $options = Scenario::getOptions()['airdrop2'];
    Globals::incAirDrops();
    $dropNumber = Globals::getAirDrops();
    $currentTurn = Globals::getTurn();
    $maxi = ($args['nb_drops'] == 1) ?  $args['nb'] : $args['nb'][$dropNumber - 1];


    for ($i = 0; $i < $maxi; $i++) {
      $pos = Board::randomWalk(['x' => $x, 'y' => $y], $options['range']);
      $unit = Units::addInCell($options['unit'], $pos);
      if (
        isset($options['unit']['behavior'])
        && $options['unit']['behavior'] == 'CANNOT_BE_ACTIVATED_TILL_TURN'
      ) {
        //$unit -> setExtraDatas('cannotBeActivated', true);
        $unit->setExtraDatas('cannotBeActivatedUntilTurn', $options['unit']['turn'] + $currentTurn);
      }

      $fails = 0;
      if (is_null($pos) || Board::isImpassableCell($pos, $unit) || Board::getUnitInCell($pos) !== null) {
        // Unsafed landing => remove unit
        Units::remove($unit->getId());
        $fails++;
      } else {
        // Notify about it
        Board::addUnit($unit);
        Notifications::airDrop($player, $unit);
      }

      if ($fails > 0) {
        Notifications::message(\clienttranslate('${player_name} unsuccessfully air drop ${n} unit(s)'), [
          'player' => $player,
          'n' => $fails,
        ]);
      }
    }
    if (Globals::getAirDrops() >= $args['nb_drops']) {
      $this->gamestate->jumpToState(ST_PREPARE_TURN);
      Globals::setAirDrops(0);
    } else {
      $this->gamestate->jumpToState(ST_AIR_DROP);
    }
  }
}
