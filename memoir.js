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
      this._activeStates = [
        'playCard',
        'orderUnits',
        'moveUnits',
        'attackUnits',
        'opponentAmbush',
        'drawChoice',
        'targetMedics',
        'targetAirPower',
        'targetBarrage',
        'airDrop',
      ];
      this._notifications = [
        ['playCard', 1000],
        ['discardCard', 1200],
        ['discardCards', 1200],
        ['drawCards', 1000],
        ['pDrawCards', 1000],
        ['activateUnits', 2],
        ['moveUnit', 600],
        ['rollDice', 3300],
        ['clearUnitsStatus', 2],
        ['takeDamage', 1000],
        ['miss', 200],
        ['healUnit', 800],
        ['removeObstacle', 200],
        ['addObstacle', 200],
        ['reshuffle', 1000],
        ['scoreMedals', 1000],
        ['removeMedals', 1000],
        ['refreshInterface', 100],
        ['airDrop', 500],
      ];

      // Fix mobile viewport (remove CSS zoom)
      this.default_viewport = 'width=700';

      this._backCardIdCounter = -1; // Used to generate unique id for backCards
      this._boardTooltips = {}; // Used to store pending board tooltip element
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

      // WHich player point of vue are we going to take ?
      this._pId = this.isSpectator ? Object.keys(this.gamedatas.players)[0] : this.player_id;
      this._bottomTeam = this.gamedatas.players[this._pId].team;

      // Load board
      if (gamedatas.board) {
        this.setupBoard();
      }

      this.setupPlayers();
      this.setupTeams();

      // Handle deck and discard
      this._deckCounter = this.createCounter('deck-count', gamedatas.deckCount);
      if (gamedatas.discard) {
        this.addCard(gamedatas.discard, 'discard');
      }
    },

    clearInterface() {
      dojo.empty('m44-board-terrains');
      dojo.empty('m44-board-units');
      dojo.empty('m44-board-labels');

      dojo.empty('top-medals-slots');
      dojo.empty('top-medals-container');
      dojo.empty('bottom-medals-slots');
      dojo.empty('bottom-medals-container');

      if ($('m44-player-hand')) {
        dojo.empty('m44-player-hand');
      }
      dojo.query('.card-in-play').empty();

      this.forEachPlayer((player) => {
        this._handCounters[player.id].setValue(0);
      });

      dojo.empty('discard');
      dojo.destroy('scenario-dropzone');
    },

    onEnteringState(stateName, args) {
      this.inherited(arguments);

      // Highlight current attack
      if (args.args && args.args.currentAttack) {
        let attack = args.args.currentAttack;
        if (attack.unitId) {
          $(`unit-${attack.unitId}`).classList.add('attacking');
        }

        if (attack.oppUnitId) {
          $(`unit-${attack.oppUnitId}`).classList.add('attacked');
        } else {
          // TODO : useless ?
          $(`cell-${attack.x}-${attack.y}`).classList.add('attacked');
        }
      }
    },

    notif_refreshInterface(n) {
      debug('Refreshing the interface', n);
      this.clearInterface();

      // Update gamedatas
      this.gamedatas.players = n.args.players;
      this.gamedatas.board = n.args.board;
      this.gamedatas.teams = n.args.teams;
      this.gamedatas.terrains = n.args.terrains;
      this.gamedatas.units = n.args.units;
      this._bottomTeam = this.gamedatas.players[this._pId].team;
      this._deckCounter.setValue(n.args.deckCount);

      this.setupTeams();
      this.setupBoard();
    },

    clearPossible() {
      this.inherited(arguments);
      [
        'moving',
        'forMove',
        'forMoveAndAttack',
        'attacking',
        'forAttack',
        'retreating',
        'forRetreat',
        'attacked',
      ].forEach((className) => {
        this.removeClassNameOfCells(className);
      });
      $('m44-board').classList.remove('displayLineOfSight');
      dojo.query('.choice').removeClass('choice');
      dojo.query('.dice-mini').forEach(dojo.destroy);
    },

    displayScenarioLoader() {
      this.clearInterface();
      dojo.place(
        `<div id="scenario-dropzone" class="dropzone" style="position:absolute; z-index:12; display: flex; flex-flow: column;justify-content: center; align-items: center;">
          <svg style="width:100px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M384 0v128h128L384 0zM352 128L352 0H176C149.5 0 128 21.49 128 48V288h174.1l-39.03-39.03c-9.375-9.375-9.375-24.56 0-33.94s24.56-9.375 33.94 0l80 80c9.375 9.375 9.375 24.56 0 33.94l-80 80c-9.375 9.375-24.56 9.375-33.94 0C258.3 404.3 256 398.2 256 392s2.344-12.28 7.031-16.97L302.1 336H128v128C128 490.5 149.5 512 176 512h288c26.51 0 48-21.49 48-48V160h-127.1C366.3 160 352 145.7 352 128zM24 288C10.75 288 0 298.7 0 312c0 13.25 10.75 24 24 24H128V288H24z"/></svg>

          <input type="file" id="scenario-input" />
           <label for="scenario-input">Choose scenario</label>
      </div>`,
        'm44-board',
      );

      let handleImages = (files) => {
        let file = files[0];
        const reader = new FileReader();
        reader.readAsText(file);
        reader.addEventListener('load', (e) => {
          let content = e.target.result;
          let scenario = JSON.parse(content);
          this.takeAction('actUploadScenario', { scenario: JSON.stringify(scenario) }, false);
        });
      };

      $('scenario-input').addEventListener('change', (e) => {
        const { files } = e.target;
        handleImages(files);
      });
    },

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
