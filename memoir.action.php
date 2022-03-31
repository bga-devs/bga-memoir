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

  public function actUploadScenario()
  {
    self::setAjaxMode();
    $scenario = self::getArg('scenario', AT_json, true);
    $this->game->actUploadScenario($scenario);
    self::ajaxResponse();
  }

  public function actPlayCard()
  {
    self::setAjaxMode();
    $cardId = self::getArg('cardId', AT_posint, true);
    $section = self::getArg('section', AT_posint, false);
    $hill = self::getArg('hill317', AT_bool, false);
    $this->game->actPlayCard($cardId, $section, $hill);
    self::ajaxResponse();
  }

  public function actCommissarCard()
  {
    self::setAjaxMode();
    $cardId = self::getArg('cardId', AT_posint, true);
    $this->game->actCommissarCard($cardId);
    self::ajaxResponse();
  }

  public function actPlayCommissarCard()
  {
    self::setAjaxMode();
    $section = self::getArg('section', AT_posint, false);
    $hill = self::getArg('hill317', AT_bool, false);
    $this->game->actPlayCommissarCard($section, $hill);
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

  public function actOrderUnitsFinestHour()
  {
    self::setAjaxMode();
    $unitIds = $this->getNumberList('unitIds');
    $this->game->actOrderUnitsFinestHour($unitIds);
    self::ajaxResponse();
  }

  public function actTargetMedics()
  {
    self::setAjaxMode();
    $unitId = self::getArg('unitId', AT_posint, true);
    $this->game->actTargetMedics($unitId);
    self::ajaxResponse();
  }

  public function actTargetBarrage()
  {
    self::setAjaxMode();
    $unitId = self::getArg('unitId', AT_posint, true);
    $this->game->actTargetBarrage($unitId);
    self::ajaxResponse();
  }

  public function actTargetAirPower()
  {
    self::setAjaxMode();
    $unitIds = $this->getNumberList('unitIds');
    $this->game->actTargetAirPower($unitIds);
    self::ajaxResponse();
  }

  public function actChooseCard()
  {
    self::setAjaxMode();
    $cardId = self::getArg('cardId', AT_posint, false);
    $this->game->actChooseCard($cardId);
    self::ajaxResponse();
  }

  public function actAirDrop()
  {
    self::setAjaxMode();
    $x = self::getArg('x', AT_posint, false);
    $y = self::getArg('y', AT_posint, false);
    $this->game->actAirDrop($x, $y);
    self::ajaxResponse();
  }

  public function actHealUnit()
  {
    self::setAjaxMode();
    $unitId = self::getArg('unitId', AT_posint, false);
    $this->game->actHealUnit($unitId);
    self::ajaxResponse();
  }

  public function actExitUnit()
  {
    self::setAjaxMode();
    $unitId = self::getArg('unitId', AT_posint, false);
    $this->game->actExitUnit($unitId);
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

  public function actNextAttack()
  {
    self::setAjaxMode();
    $this->game->actNextAttack();
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

  public function actRemoveWire()
  {
    self::setAjaxMode();
    $unitId = self::getArg('unitId', AT_posint, false);
    $this->game->actRemoveWire($unitId);
    self::ajaxResponse();
  }

  public function actRemoveRoadBlock()
  {
    self::setAjaxMode();
    $unitId = self::getArg('unitId', AT_posint, false);
    $this->game->actRemoveRoadBlock($unitId);
    self::ajaxResponse();
  }

  public function actBattleBack()
  {
    self::setAjaxMode();
    $this->game->actBattleBack();
    self::ajaxResponse();
  }

  public function actBattleBackPass()
  {
    self::setAjaxMode();
    $this->game->actBattleBackPass();
    self::ajaxResponse();
  }

  ///////////////////////////////////////////////////
  //    ____  _____ _____ ____  _____    _  _____
  //   |  _ \| ____|_   _|  _ \| ____|  / \|_   _|
  //   | |_) |  _|   | | | |_) |  _|   / _ \ | |
  //   |  _ <| |___  | | |  _ <| |___ / ___ \| |
  //   |_| \_\_____| |_| |_| \_\_____/_/   \_\_|
  ///////////////////////////////////////////////////

  public function actRetreatUnit()
  {
    self::setAjaxMode();
    $x = self::getArg('x', AT_posint, false);
    $y = self::getArg('y', AT_posint, false);
    $this->game->actRetreatUnit($x, $y);
    self::ajaxResponse();
  }

  public function actRetreatUnitDone()
  {
    self::setAjaxMode();
    $this->game->actRetreatUnitDone();
    self::ajaxResponse();
  }

  ////////////////////////////////////////////////////////////////
  //  _____     _           ____                           _
  // |_   _|_ _| | _____   / ___|_ __ ___  _   _ _ __   __| |
  //   | |/ _` | |/ / _ \ | |  _| '__/ _ \| | | | '_ \ / _` |
  //   | | (_| |   <  __/ | |_| | | | (_) | |_| | | | | (_| |
  //   |_|\__,_|_|\_\___|  \____|_|  \___/ \__,_|_| |_|\__,_|
  ///////////////////////////////////////////////////////////////
  public function actTakeGround()
  {
    self::setAjaxMode();
    $this->game->actTakeGround();
    self::ajaxResponse();
  }

  public function actPassTakeGround()
  {
    self::setAjaxMode();
    $this->game->actPassTakeGround();
    self::ajaxResponse();
  }

  ////////////////////////////////////////////////
  //     _              _               _
  //    / \   _ __ ___ | |__  _   _ ___| |__
  //   / _ \ | '_ ` _ \| '_ \| | | / __| '_ \
  //  / ___ \| | | | | | |_) | |_| \__ \ | | |
  // /_/   \_\_| |_| |_|_.__/ \__,_|___/_| |_|
  ////////////////////////////////////////////////

  public function actPassAmbush()
  {
    self::setAjaxMode();
    $this->game->actPassAmbush();
    self::ajaxResponse();
  }

  public function actAmbush()
  {
    self::setAjaxMode();
    $this->game->actAmbush();
    self::ajaxResponse();
  }

  /////////////////////////////////
  //  _   _ _____ ___ _     ____
  // | | | |_   _|_ _| |   / ___|
  // | | | | | |  | || |   \___ \
  // | |_| | | |  | || |___ ___) |
  //  \___/  |_| |___|_____|____/
  /////////////////////////////////

  public function getNumberList($name, $mandatory = true)
  {
    $t = $this->getArg($name, AT_numberlist, $mandatory);
    return $t == '' ? [] : explode(';', $t);
  }

  public function validateJSonAlphaNum($value, $argName = 'unknown')
  {
    if (is_array($value)) {
      foreach ($value as $key => $v) {
        $this->validateJSonAlphaNum($key, $argName);
        $this->validateJSonAlphaNum($v, $argName);
      }
      return true;
    }
    if (is_int($value)) {
      return true;
    }
    $bValid = preg_match("/^[_0-9a-zA-Z- ]*$/", $value) === 1;
    if (!$bValid) {
      throw new feException("Bad value for: $argName", true, true, FEX_bad_input_argument);
    }
    return true;
  }
}
