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
        ['discardCard', 1200],
        ['drawCards', 1000],
        ['pDrawCards', 1000],
        ['activateUnits', 2],
        ['moveUnit', 600],
        ['rollDice', 3300],
        ['clearUnitsStatus', 2],
        ['takeDamage', 1000],
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

      // Handle deck and discard
      this._deckCounter = this.createCounter('deck-count', gamedatas.deckCount);
      if (gamedatas.discard) {
        this.addCard(gamedatas.discard, 'discard');
      }
    },

    clearPossible() {
      this.inherited(arguments);
      ['moving', 'forMove', 'attacking', 'forAttack', 'retreating', 'forRetreat'].forEach((className) => {
        this.removeClassNameOfCells(className);
      });
      $('m44-board').classList.remove('displayLineOfSight');
      dojo.query('.dice-mini').forEach(dojo.destroy);
    },

    updatePlayerOrdering() {},

    /* This enable to inject translatable styled things to logs or action bar */
    /* @Override */
    format_string_recursive(log, args) {
      try {
        if (log && args && !args.processed) {
          args.processed = true;

          // Representation of the value of a dice
          if (args.dice_result !== undefined) {
            args.dice_face = `<span class='m44-dice-result' data-result='${args.dice_result}'></span>`;
          }
        }
      } catch (e) {
        console.error(log, args, 'Exception thrown', e.stack);
      }

      return this.inherited(arguments);
    },
  });
});
