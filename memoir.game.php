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
use M44\Core\Notifications;
use M44\Core\Stats;
use M44\Helpers\Log;
use M44\Managers\Cards;
use M44\Managers\Players;
use M44\Managers\Terrains;
use M44\Managers\Teams;
use M44\Managers\Units;
use M44\Board;
use M44\Scenario;

class memoir extends Table
{
  use M44\DebugTrait;
  use M44\States\LoadScenarioTrait;
  use M44\States\RoundTrait;
  use M44\States\TurnTrait;
  use M44\States\PlayCardTrait;
  use M44\States\OrderUnitsTrait;
  use M44\States\MoveUnitsTrait;
  use M44\States\AttackUnitsTrait;
  use M44\States\DrawCardsTrait;
  use M44\States\AmbushTrait;
  use M44\States\RetreatUnitTrait;
  use M44\States\TakeGroundTrait;
  use M44\States\TacticCardTrait;
  use M44\States\AirDropTrait;
  use M44\States\CommissarCardTrait;
  use M44\States\ConfirmRestartTrait;

  public static $instance = null;
  function __construct()
  {
    parent::__construct();
    self::$instance = $this;
    self::initGameStateLabels([
      'logging' => 10,
    ]);
    Board::init();
    Stats::checkExistence();
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
    Stats::checkExistence();

    $this->setGameStateInitialValue('logging', false);
    $this->activeNextPlayer();
  }

  /*
   * getAllDatas:
   */
  public function getAllDatas($pId = null)
  {
    $pId = $pId ?? self::getCurrentPId();
    $scenario = Scenario::get();
    $campaign = Globals::isCampaign() ? Globals::getCampaign() : null;
    return [
      'localPrefs' => Preferences::getLocalPrefsData(),
      'prefs' => Preferences::getUiData($pId),
      'players' => Players::getUiData($pId),
      'board' => Board::getUiData(),
      'teams' => Teams::getAll()->toJsonArray(),
      'deckCount' => Cards::countInLocation('deck'),
      'discard' => Cards::getTopOf('discard'),
      'scenario' => is_null($scenario) ? null : $scenario,
      'campaign' => is_null($campaign) ? null : $campaign,

      'duration' => 3 - Globals::getDuration(),
      'round' => Globals::getRound(),
      'stats' => Stats::getUiData(),

      'terrains' => Terrains::getStaticUiData(),
      'units' => Units::getStaticUiData(),

      'canceledNotifIds' => Log::getCanceledNotifIds(),
      'visibility' => Globals::getNightVisibility(),
      'turn' => Globals::getTurn(),
      'isCampaign' => Globals::isCampaign(),
    ];
  }

  /*
   * getGameProgression:
   */
  function getGameProgression()
  {
    $progression = 0;
    foreach (Teams::getAll() as $team) {
      $progression = max(round(($team->getMedals()->count() / $team->getNVictory()) * 100), $progression);
    }
    if (Globals::getDuration() == OPTION_DURATION_TWO_WAYS) {
      $progression = intdiv($progression, 2) + (Globals::getRound() == 1 ? 0 : 50);
    }
    return $progression;
  }

  function startGame($c)
  {
    Scenario::setup($c == 1, true);
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
    if (Globals::getLogState() == -1) {
      Globals::setLogState($state);
      Globals::setActionCount(0);
      Globals::setAirDrops(0);
      Log::clearAll();
    }

    Globals::setChangeActivePlayer([
      'pId' => is_int($pId) ? $pId : $pId->getId(),
      'st' => $state,
    ]);
    $this->gamestate->jumpToState(ST_CHANGE_ACTIVE_PLAYER);
  }

  function nextState($transition, $pId = null)
  {
    $state = $this->gamestate->state(true, false, true);
    $st = $state['transitions'][$transition];

    if (Globals::getLogState() == -1) {
      Globals::setLogState($st);
      Globals::setActionCount(0);
      Log::clearAll();
    }

    $pId = is_null($pId) || is_int($pId) ? $pId : $pId->getId();
    if (is_null($pId) || $pId == $this->getActivePlayerId()) {
      $this->gamestate->nextState($transition);
    } else {
      if ($state['type'] == 'game') {
        $this->gamestate->changeActivePlayer($pId);
        $this->gamestate->nextState($transition);
      } else {
        $this->changeActivePlayerAndJumpTo($pId, $st);
      }
    }
  }

  /////////////////////////////////////////////////////////////
  // Exposing protected methods, please use at your own risk //
  /////////////////////////////////////////////////////////////

  // Exposing protected method getCurrentPlayerId
  public static function getCurrentPId()
  {
    return self::get()->getCurrentPlayerId();
  }

  // Exposing protected method translation
  public static function translate($text)
  {
    return self::get()->_($text);
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
      $this->gamestate->setPlayerNonMultiactive($activePlayer, '');

      return;
    }

    throw new feException('Zombie mode not supported at this game state: ' . $statename);
  }

  /////////////////////////////////////Globals
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
    if ($from_version <= 2204111537) {
      $result = self::getUniqueValueFromDB("SHOW COLUMNS FROM `gamelog` LIKE 'cancel'");
      if (is_null($result)) {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `move_id` int(10) NOT NULL,
  `table` varchar(32) NOT NULL,
  `primary` varchar(32) NOT NULL,
  `type` varchar(32) NOT NULL,
  `affected` JSON,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
        self::applyDbUpgradeToAllDB($sql);

        $sql = 'ALTER TABLE `DBPREFIX_gamelog` ADD `cancel` TINYINT(1) NOT NULL DEFAULT 0';
        self::applyDbUpgradeToAllDB($sql);
      }
    }

    if ($from_version <= 2204130041) {
      Globals::setNightVisibility(INFINITY);
    }

    if ($from_version <= 2409031042) {
      $result = self::getUniqueValueFromDB("SHOW COLUMNS FROM `teams` LIKE 'reserve_tokens'");
      if (is_null($result)) {
        $sql = 'ALTER TABLE `DBPREFIX_teams` ADD `reserve_tokens` INT(10) NOT NULL DEFAULT 0';
        self::applyDbUpgradeToAllDB($sql);
      }
    }

    if ($from_version <= 2410212115) {
      Globals::setCampaign([]);
      Globals::setCampaignId(0);
      Globals::setCampaignStep(0);
    }

    if ($from_version <= 2412151602) {
      Globals::setAirStrikes(null);
    }

    if ($from_version <=  2502171920) {
      Globals::setArmorBreakthrough(null);
    }

    if ($from_version <=  2502231610) {
      Globals::setArmorBreakthroughDone(['AXIS' => false, 'ALLIES' => false]);
    }

    if ($from_version <=  2503091812) {
      if (Globals::isCampaign()) {
        // reload current campaign in order to have all campaign infomation for briefing in the database
        $campaign = Globals::getCampaign();
        $campaignId = $campaign['campaignId'];
        Scenario::campaignloadId($campaignId);
      }
    }

    if ($from_version <= 2510051743) {
      if (Globals::isCampaign()) {
        // remove campaign stats from database in order to prevent large notification >128k error
        for ($i=60; $i < 94 ; $i++) { // stat type
          foreach (Players::getAll() as $player) {
            $stat = Stats::getFilteredQuery2($i, $player->getId())->get(true);
            if (isset($stat['id'])) {
              Stats::DB()->delete($stat['id']);
            } 
            
          }
        }
      }
    }

  }
}
