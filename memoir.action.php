<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * memoir implementation : ©  Timothée Pecatte <tim.pecatte@gmail.com>, Vincent Toper <vincent.toper@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 *
 * memoir.action.php
 *
 * memoir main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/memoir/memoir/myAction.html", ...)
 *
 */

class action_memoir extends APP_GameAction
{
  // Constructor: please do not modify
  public function __default()
  {
    if (self::isArg('notifwindow')) {
      $this->view = 'common_notifwindow';
      $this->viewArgs['table'] = self::getArg('table', AT_posint, true);
    } else {
      $this->view = 'memoir_memoir';
      self::trace('Complete reinitialization of board game');
    }
  }

  public function actPlayCard()
  {
    self::setAjaxMode();
    $cardId = self::getArg('cardId', AT_posint, false);
    $this->game->actPlayCard($cardId);
    self::ajaxResponse();
  }

  public function actOrderUnits()
  {
    self::setAjaxMode();
    $unitIds = $this->getNumberList('unitIds');
    $onTheMoveIds = $this->getNumberList('unitOnTheMoveIds');
    $this->game->actOrderUnits($unitIds, $onTheMoveIds);
    self::ajaxResponse();
  }


  /////////////////////////////////
  //  __  __  _____     _______
  // |  \/  |/ _ \ \   / / ____|
  // | |\/| | | | \ \ / /|  _|
  // | |  | | |_| |\ V / | |___
  // |_|  |_|\___/  \_/  |_____|
  /////////////////////////////////

  public function actMoveUnit()
  {
    self::setAjaxMode();
    $unitId = self::getArg('unitId', AT_posint, false);
    $x = self::getArg('x', AT_posint, false);
    $y = self::getArg('y', AT_posint, false);
    $this->game->actMoveUnit($unitId, $x, $y);
    self::ajaxResponse();
  }

  public function actMoveUnitsDone()
  {
    self::setAjaxMode();
    $this->game->actMoveUnitsDone();
    self::ajaxResponse();
  }


  //////////////////////////////////////////
  //    _  _____ _____  _    ____ _  __
  //    / \|_   _|_   _|/ \  / ___| |/ /
  //   / _ \ | |   | | / _ \| |   | ' /
  //  / ___ \| |   | |/ ___ \ |___| . \
  // /_/   \_\_|   |_/_/   \_\____|_|\_\
  //////////////////////////////////////////

  public function actAttackUnitsDone()
  {
    self::setAjaxMode();
    $this->game->actAttackUnitsDone();
    self::ajaxResponse();
  }

  public function actAttackUnit()
  {
    self::setAjaxMode();
    $unitId = self::getArg('unitId', AT_posint, false);
    $x = self::getArg('x', AT_posint, false);
    $y = self::getArg('y', AT_posint, false);
    $this->game->actAttackUnit($unitId, $x, $y);
    self::ajaxResponse();
  }


  public function getNumberList($name, $mandatory = true)
  {
    $t = $this->getArg($name, AT_numberlist, $mandatory);
    return $t == '' ? [] : explode(';', $t);
  }
}
