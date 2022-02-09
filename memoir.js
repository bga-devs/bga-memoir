/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * memoir implementation : ©  Timothée Pecatte <tim.pecatte@gmail.com>, Vincent Toper <vincent.toper@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * memoir.js
 *
 * memoir user interface script
 *
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

var isDebug = window.location.host == 'studio.boardgamearena.com' || window.location.hash.indexOf('debug') > -1;
var debug = isDebug ? console.info.bind(window.console) : function () {};

define([
  'dojo',
  'dojo/_base/declare',
  'ebg/core/gamegui',
  'ebg/counter',
  g_gamethemeurl + 'modules/js/Core/game.js',
  g_gamethemeurl + 'modules/js/Core/modal.js',
  g_gamethemeurl + 'modules/js/Board.js',
  g_gamethemeurl + 'modules/js/Players.js',
  g_gamethemeurl + 'modules/js/States/OrderUnits.js',
], function (dojo, declare) {
  return declare('bgagame.memoir', [customgame.game, memoir.board, memoir.players, memoir.orderUnits], {
    constructor() {
      this._activeStates = ['playCard', 'orderUnits', 'moveUnits', 'attackUnits'];
      this._notifications = [
        ['playCard', 1000],
        ['moveUnit', 1200],
      ];

      // Fix mobile viewport (remove CSS zoom)
      this.default_viewport = 'width=700';

      this._backCardIdCounter = -1; // Used to generate unique id for backCards
    },

    /**
     * Setup:
     *	This method set up the game user interface according to current game situation specified in parameters
     *	The method is called each time the game interface is displayed to a player, ie: when the game starts and when a player refreshes the game page (F5)
     *
     * Params :
     *	- mixed gamedatas : contains all datas retrieved by the getAllDatas PHP method.
     */
    setup(gamedatas) {
      debug('SETUP', gamedatas);
      this.inherited(arguments);

      this.setupPlayers();

      // Load board
      if (gamedatas.board) {
        let pId = this.isSpectator ? Object.values(this.gamedatas.players)[0] : this.player_id;
        let bottomTeam = this.gamedatas.players[pId].team;
        let rotate = this.gamedatas.players[pId].no == 1;
        this.setupBoard(gamedatas.board, rotate, bottomTeam);
      }
    },

    clearPossible() {
      this.inherited(arguments);
      ['moving', 'forMove', 'attacking', 'forAttack'].forEach((className) => {
        this.removeClassNameOfCells(className);
      });
      $('m44-board').classList.remove('displayLineOfSight');
      dojo.query('.dice-mini').forEach(dojo.destroy);
    },

    updatePlayerOrdering() {},
  });
});
