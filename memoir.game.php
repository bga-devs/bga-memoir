<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * memoir implementation : ©  Timothée Pecatte <tim.pecatte@gmail.com>, Vincent Toper <vincent.toper@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * memoir.game.php
 *
 * This is the main file for your game logic.
 *
 * In this PHP file, you are going to defines the rules of the game.
 *
 */

$swdNamespaceAutoload = function ($class) {
  $classParts = explode('\\', $class);
  if ($classParts[0] == 'M44') {
    array_shift($classParts);
    $file = dirname(__FILE__) . '/modules/php/' . implode(DIRECTORY_SEPARATOR, $classParts) . '.php';
    if (file_exists($file)) {
      require_once $file;
    } else {
      var_dump('Cannot find file : ' . $file);
    }
  }
};
spl_autoload_register($swdNamespaceAutoload, true, true);

require_once APP_GAMEMODULE_PATH . 'module/table/table.game.php';

use M44\Core\Globals;
use M44\Core\Preferences;
use M44\Managers\Cards;
use M44\Managers\Players;
use M44\Managers\Teams;
use M44\Board;
use M44\Scenario;

class memoir extends Table
{
  use M44\DebugTrait;
  use M44\States\PrepareTurnTrait;
  use M44\States\PlayCardTrait;
  use M44\States\OrderUnitsTrait;
  use M44\States\MoveUnitsTrait;
  use M44\States\AttackUnitsTrait;
  use M44\States\DrawCardsTrait;
  use M44\States\AmbushTrait;
  use M44\States\RetreatUnitTrait;

  public static $instance = null;
  function __construct()
  {
    parent::__construct();
    self::$instance = $this;
    self::initGameStateLabels([
      'logging' => 10,
      'scenario' => OPTION_SCENARIO,
    ]);
    Board::init();
  }

  public static function get()
  {
    return self::$instance;
  }

  protected function getGameName()
  {
    return 'memoir';
  }

  /*
   * setupNewGame:
   */
  protected function setupNewGame($players, $options = [])
  {
    Globals::setupNewGame($players, $options);
    Preferences::setupNewGame($players, $options);
    Players::setupNewGame($players, $options);

    $this->activeNextPlayer();
  }

  public function stDummyState()
  {
    $scenario = (int) self::getGameStateValue('scenario');
    Scenario::load($scenario);
    Scenario::setup();
  }

  /*
   * getAllDatas:
   */
  public function getAllDatas()
  {
    $pId = self::getCurrentPId();
    return [
      'prefs' => Preferences::getUiData($pId),
      'players' => Players::getUiData($pId),
      'board' => Board::getUiData(),
      'teams' => Teams::getAll()->toArray(),
      'deckCount' => Cards::countInLocation('deck'),
      'discard' => Cards::getTopOf('discard'),
    ];
  }

  /*
   * getGameProgression:
   */
  function getGameProgression()
  {
    return 50; // TODO
  }

  function actChangePreference($pref, $value)
  {
    Preferences::set($this->getCurrentPId(), $pref, $value);
  }

  /**
   * Generic state to handle change of active player in the middle of a transition
   */
  function stChangeActivePlayer()
  {
    $t = Globals::getChangeActivePlayer();
    $this->gamestate->changeActivePlayer($t['pId']);
    $this->gamestate->jumpToState($t['st']);
  }

  function changeActivePlayerAndJumpTo($pId, $state)
  {
    Globals::setChangeActivePlayer([
      'pId' => is_int($pId) ? $pId : $pId->getId(),
      'st' => $state,
    ]);
    $this->gamestate->jumpToState(ST_CHANGE_ACTIVE_PLAYER);
  }

  function nextState($transition, $pId = null)
  {
    $pId = is_null($pId) || is_int($pId) ? $pId : $pId->getId();
    if ($pId === null || $pId == $this->getActivePlayerId()) {
      $this->gamestate->nextState($transition);
    } else {
      $states = $this->gamestate->states;
      $state = $states[$this->gamestate->state_id()];
      $st = $state['transitions'][$transition];
      $this->changeActivePlayerAndJumpTo($pId, $st);
    }
  }

  /////////////////////////////////////////////////////////////
  // Exposing protected methods, please use at your own risk //
  /////////////////////////////////////////////////////////////

  // Exposing protected method getCurrentPlayerId
  public static function getCurrentPId()
  {
    return self::getCurrentPlayerId();
  }

  // Exposing protected method translation
  public static function translate($text)
  {
    return self::_($text);
  }

  ////////////////////////////////////
  ////////////   Zombie   ////////////
  ////////////////////////////////////
  /*
   * zombieTurn:
   *   This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
   *   You can do whatever you want in order to make sure the turn of this player ends appropriately
   */
  public function zombieTurn($state, $activePlayer)
  {
    $statename = $state['name'];

    if ($state['type'] === 'activeplayer') {
      switch ($statename) {
        default:
          $this->gamestate->nextState('zombiePass');
          break;
      }

      return;
    }

    if ($state['type'] === 'multipleactiveplayer') {
      // Make sure player is in a non blocking status for role turn
      $this->gamestate->setPlayerNonMultiactive($active_player, '');

      return;
    }

    throw new feException('Zombie mode not supported at this game state: ' . $statename);
  }

  /////////////////////////////////////
  //////////   DB upgrade   ///////////
  /////////////////////////////////////
  // You don't have to care about this until your game has been published on BGA.
  // Once your game is on BGA, this method is called everytime the system detects a game running with your old Database scheme.
  // In this case, if you change your Database scheme, you just have to apply the needed changes in order to
  //   update the game database and allow the game to continue to run with your new version.
  /////////////////////////////////////
  /*
   * upgradeTableDb
   *  - int $from_version : current version of this game database, in numerical form.
   *      For example, if the game was running with a release of your game named "140430-1345", $from_version is equal to 1404301345
   */
  public function upgradeTableDb($from_version)
  {
  }
}
