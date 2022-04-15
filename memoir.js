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
        'commissarCard',
        'playCommissarCard',
        'orderUnits',
        'moveUnits',
        'opponentAmbush',
        'battleBack',
        'drawChoice',
        'targetMedics',
        'targetAirPower',
        'targetBarrage',
        'airDrop',
      ];
      this._notifications = [
        ['playCard', 1000],
        ['discardCard', 1200],
        ['discardCardItalianHighCommand', 1200],
        ['discardCards', 1200],
        ['drawCards', 1000],
        ['pDrawCards', 1000],
        ['activateUnits', 2],
        ['moveUnit', 600],
        ['throwAttack', 300],
        ['rollDice', 3300],
        ['clearUnitsStatus', 2],
        ['takeDamage', 1000],
        ['miss', 200],
        ['healUnit', 800],
        ['removeTerrain', 200],
        ['addTerrain', 200],
        ['reshuffle', 1000],
        ['scoreMedals', 1000],
        ['removeMedals', 1000],
        ['removeSectionMedals', 1000],
        ['refreshInterface', 100],
        ['airDrop', 500],
        ['addToken', 1000],
        ['removeToken', 1000],
        ['revealMinefield', 100],
        ['commissarCard', 1000],
        ['pCommissarCard', 1000],
        ['revealCommissarCard', 1000],
        ['exitUnit', 500],
        ['clearTurn', 1],
        ['smallRefreshInterface', 1],
        ['smallRefreshHand', 1],
        ['updateVisibility', 500],
      ];

      // Fix mobile viewport (remove CSS zoom)
      this.default_viewport = 'width=700';

      this._backCardIdCounter = -1; // Used to generate unique id for backCards
      this._boardTooltips = {}; // Used to store pending board tooltip element

      this._settingsConfig = {
        layout: {
          default: 0,
          name: _('Layout'),
          attribute: 'layout',
          type: 'select',
          values: {
            0: _('Compact'),
            1: _('Standard'),
          },
        },
        boardScale: {
          default: 100,
          name: _('Board scale'),
          type: 'slider',
          sliderConfig: {
            step: 5,
            padding: 10,
            range: {
              min: [50],
              max: [200],
            },
          },
        },
        centralZone: {
          default: 0,
          name: _('Central zone behavior'),
          attribute: 'centralZone',
          type: 'select',
          values: {
            0: _('Fixed height (scrollable board)'),
            1: _('Dynamic (no scroll)'),
          },
        },
        centralZoneHeight: {
          default: 600,
          name: _('Central zone height'),
          type: 'slider',
          sliderConfig: {
            step: 5,
            padding: 10,
            range: {
              min: [500],
              max: [1000],
            },
          },
        },
        cardScale: {
          default: 70,
          name: _('Card scale'),
          type: 'slider',
          sliderConfig: {
            step: 5,
            padding: 10,
            range: {
              min: [10],
              max: [100],
            },
          },
        },
        autoPass: { type: 'pref', prefId: 150 },
      };
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

      // Basic twist of UI
      this.setupInfoPanel();
      dojo.place('<div id="title-content-wrapper"></div>', 'after-page-title', 'before');
      [
        'after-page-title',
        'page-title',
        'arena_cannot_play_panel',
        'table-decision',
        'zombieBack',
        'connect_status',
        'connect_gs_status',
        'arena_ending_soon',
        'log_history_status',
        'page-content',
      ].forEach((elt) => {
        if ($(elt)) {
          dojo.place(elt, 'title-content-wrapper');
        }
      });

      // WHich player point of vue are we going to take ?
      this._pId = this.isSpectator ? Object.keys(this.gamedatas.players)[0] : this.player_id;
      this._bottomTeam = this.gamedatas.players[this._pId].team;

      // Load board
      if (gamedatas.board) {
        this.setupBoard();
      }
      // Load scenario infos
      if (gamedatas.scenario) {
        this.setupScenario();
      }

      this.setupPlayers();
      this.setupTeams();

      // Handle deck and discard
      this._deckCounter = this.createCounter('deck-count', gamedatas.deckCount);
      if (gamedatas.discard) {
        this.addCard(gamedatas.discard, 'discard');
      }

      let container = $('pagesection_options').querySelector('.pagesection');
      dojo.place('<div id="local-prefs-container"></div>', container);
      this.inherited(arguments);
    },

    clearInterface(partial = false) {
      dojo.empty('m44-board-terrains');
      dojo.empty('m44-board-units');
      dojo.empty('m44-board-labels');

      dojo.empty('bottom-medals-container');
      dojo.empty('top-medals-container');

      dojo.empy('scenario-informations');

      if (!partial) {
        dojo.empty('bottom-medals-slots');
        dojo.empty('bottom-team-players');
        dojo.empty('bottom-in-play');
        dojo.empty('top-medals-slots');
        dojo.empty('top-team-players');
        dojo.empty('top-in-play');

        dojo.empty('scenario-informations');
        dojo.destroy('popin_showScenario_container');

        dojo.destroy('m44-player-hand');
        dojo.query('.card-in-play').empty();

        dojo.empty('discard');
        dojo.destroy('scenario-dropzone-container');

        this.updateTeamStatus('ALLIES', 'idle');
        this.forEachPlayer((player) => {
          this._handCounters[player.id].setValue(0);
        });
      } else {
        dojo.query('#bottom-in-play .m44-card').forEach(dojo.destroy);
        dojo.query('#top-in-play .m44-card').forEach(dojo.destroy);
      }
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

      // Update team status
      let statusMapping = {
        airDrop: 'para',
        commissarCard: 'commissar',
        playCommissarCard: 'command',
        playCard: 'command',
        orderUnits: 'order',
        moveUnits: 'move',
        attackUnits: 'attack',
        drawChoice: 'command',
        targetAirPower: 'attack',
        targetBarrage: 'attack',
      };

      if (Object.keys(statusMapping).includes(stateName)) {
        let pId = this.getActivePlayerId();
        let team = this.gamedatas.players[pId].team;
        this.updateTeamStatus(team, statusMapping[stateName]);
      }

      // Add restart button
      if (args.possibleactions && args.possibleactions.includes('actRestart') && this.isCurrentPlayerActive()) {
        if (stateName == 'attackUnits' && args.args.lastUnitAttacker != -1) {
          return;
        }

        this.addDangerActionButton(
          'btnRestartTurn',
          _('Restart turn'),
          () => {
            this.takeAction('actRestart');
          },
          'restartAction',
        );
      }
    },

    notif_clearTurn(n) {
      debug('Notif: restarting turn', n);
      this.cancelLogs(n.args.notifIds);
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
      this.gamedatas.scenario = n.args.scenario;
      this._bottomTeam = this.gamedatas.players[this._pId].team;
      this._deckCounter.setValue(n.args.deckCount);

      this.setupBoard();
      this.setupScenario();
      this.setupPlayers();
      this.setupTeams();
    },

    notif_smallRefreshInterface(n) {
      debug('Refreshing the interface because of an undo', n);
      this.clearInterface(true);

      // Update gamedatas
      this.gamedatas.players = n.args.players;
      this.gamedatas.board = n.args.board;
      this.gamedatas.teams = n.args.teams;
      this._bottomTeam = this.gamedatas.players[this._pId].team;
      this._deckCounter.setValue(n.args.deckCount);

      this.setupBoard();
      this.updatePlayers();
      this.updateTeams();
    },

    notif_smallRefreshHand(n) {
      debug('Refreshing your hand', n);
      dojo.query('#m44-player-hand .m44-card').forEach(dojo.destroy);
      this.gamedatas.players[this.player_id] = n.args.playerDatas;
      this.updateHand();
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
        'forAirDrop',
        'attacked',
      ].forEach((className) => {
        this.removeClassNameOfCells(className);
      });
      $('m44-board').classList.remove('displayLineOfSight', 'displayLineOfSightAttack');
      dojo.query('.choice').removeClass('choice');
      dojo.query('.dice-mini').forEach(dojo.destroy);
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

    ////////////////////////////////////
    //  __  __           _       _
    // |  \/  | ___   __| | __ _| |
    // | |\/| |/ _ \ / _` |/ _` | |
    // | |  | | (_) | (_| | (_| | |
    // |_|  |_|\___/ \__,_|\__,_|_|
    ////////////////////////////////////
    setupScenario() {
      dojo.place(
        `<div id='clipboard-button'>
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M336 64h-53.88C268.9 26.8 233.7 0 192 0S115.1 26.8 101.9 64H48C21.5 64 0 85.48 0 112v352C0 490.5 21.5 512 48 512h288c26.5 0 48-21.48 48-48v-352C384 85.48 362.5 64 336 64zM192 64c17.67 0 32 14.33 32 32c0 17.67-14.33 32-32 32S160 113.7 160 96C160 78.33 174.3 64 192 64zM272 224h-160C103.2 224 96 216.8 96 208C96 199.2 103.2 192 112 192h160C280.8 192 288 199.2 288 208S280.8 224 272 224z"/></svg>
    </div>`,
        'scenario-informations',
      );

      if (this.gamedatas.visibility <= 5) {
        dojo.place(`<div id='night-visibility' data-n='${this.gamedatas.visibility}'></div>`, 'scenario-informations');
        this.addTooltip($('night-visibility'), _('Current night visibility (Pacific Theater rules)'), '');
      }

      var dial = new customgame.modal('showScenario', {
        class: 'memoir44_popin',
        closeIcon: 'fa-times',
        openAnimation: true,
        openAnimationTarget: 'clipboard-button',
        contents: this.tplScenarioModal(),
        breakpoint: 800,
        closeAction: 'hide',
        verticalAlign: 'flex-begin',
        scale: 0.8,
        title: _(this.gamedatas.scenario.name),
      });

      this.addTooltip('clipboard-button', _('Show the scenario informations'), '');
      $('clipboard-button').addEventListener('click', () => dial.show());

      $('scenario-name').innerHTML = _(this.gamedatas.scenario.name);
    },

    notif_updateVisibility(n) {
      debug('Notif: visibility update', n);
      $('night-visibility').dataset.n = parseInt($('night-visibility').dataset.n) + n.args.star;
      if ($('night-visibility').dataset.n == 6) {
        setTimeout(() => dojo.destroy('night-visibility'), 800);
      }
    },

    tplScenarioModal() {
      let scenario = this.gamedatas.scenario;
      return (
        `
      <div id='scenario-historical'>
        <h5>${_('Historical Background')}</h5>
        ${_(scenario.historical).replace(/\n/g, '<br />')}
      </div>

      <div id='scenario-bottom-container'>
        <div id='scenario-brief'>
          <h5>${_('Briefing')}</h5>
          ${_(scenario.description).replace(/\n/g, '<br />')}
        </div>
        <div id='scenario-conditions-rules'>
          <h5>${_('Conditions of Victory')}</h5>
          ${_(scenario.victory).replace(/\n/g, '<br />')}
          ` +
        (scenario.rules === undefined
          ? ''
          : `
          <h5>${_('Special rules')}</h5>
          ${_(scenario.rules).replace(/\n/g, '<br />')}
            `) +
        `
        </div>
      </div>
    `
      );
    },

    ////////////////////////////////////////////
    //  _   _       _                 _
    // | | | |_ __ | | ___   __ _  __| |
    // | | | | '_ \| |/ _ \ / _` |/ _` |
    // | |_| | |_) | | (_) | (_| | (_| |
    //  \___/| .__/|_|\___/ \__,_|\__,_|
    //       |_|
    ////////////////////////////////////////////
    onEnteringStateUploadScenario(args) {
      this.clearInterface();
      dojo.place(
        `<div id="scenario-dropzone-container">
          <div id="scenario-dropzone">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M384 0v128h128L384 0zM352 128L352 0H176C149.5 0 128 21.49 128 48V288h174.1l-39.03-39.03c-9.375-9.375-9.375-24.56 0-33.94s24.56-9.375 33.94 0l80 80c9.375 9.375 9.375 24.56 0 33.94l-80 80c-9.375 9.375-24.56 9.375-33.94 0C258.3 404.3 256 398.2 256 392s2.344-12.28 7.031-16.97L302.1 336H128v128C128 490.5 149.5 512 176 512h288c26.51 0 48-21.49 48-48V160h-127.1C366.3 160 352 145.7 352 128zM24 288C10.75 288 0 298.7 0 312c0 13.25 10.75 24 24 24H128V288H24z"/></svg>

            <input type="file" id="scenario-input" />
            <label for="scenario-input">${_('Choose scenario')}</label>
            <h5>${_('or drag & drop your .m44 file here')}</h5>
          </div>
      </div>`,
        'm44-board-wrapper',
      );

      $('scenario-input').addEventListener('change', (e) => this.uploadScenario(e.target.files[0]));
      let dropzone = $('scenario-dropzone-container');
      let toggleActive = (b) => {
        return (e) => {
          e.preventDefault();
          dropzone.classList.toggle('active', b);
        };
      };
      dropzone.addEventListener('dragenter', toggleActive(true));
      dropzone.addEventListener('dragover', toggleActive(true));
      dropzone.addEventListener('dragleave', toggleActive(false));
      dropzone.addEventListener('drop', (e) => {
        toggleActive(false)(e);
        this.uploadScenario(e.dataTransfer.files[0]);
      });
    },

    uploadScenario(file) {
      const reader = new FileReader();
      reader.readAsText(file);
      reader.addEventListener('load', (e) => {
        let content = e.target.result;
        let scenario = JSON.parse(content);
        this.takeAction('actUploadScenario', { scenario: JSON.stringify(scenario), method: 'post' });
      });
    },

    //////////////////////////////////////////////////
    //  ____       _   _   _
    // / ___|  ___| |_| |_(_)_ __   __ _ ___
    // \___ \ / _ \ __| __| | '_ \ / _` / __|
    //  ___) |  __/ |_| |_| | | | | (_| \__ \
    // |____/ \___|\__|\__|_|_| |_|\__, |___/
    //                             |___/
    //////////////////////////////////////////////////

    setupInfoPanel() {
      dojo.place(this.format_string(jstpl_configPlayerBoard, {}), 'player_boards', 'first');

/*
      let chk = $('help-mode-chk');
      dojo.connect(chk, 'onchange', () => this.toggleHelpMode(chk.checked));
      this.addTooltip('help-mode-switch', '', _('Toggle help/safe mode.'));
*/
    },

    onChangeCardScaleSetting(scale) {
      let elt = $('m44-player-hand');
      if (elt) {
        elt.style.setProperty('--memoirCardScale', (scale * 0.55) / 100);
      }
    },

    onChangeBoardScaleSetting(scale) {
      document.documentElement.style.setProperty('--memoirBoardScale', scale / 100);
    },

    onChangeCentralZoneHeightSetting(scale) {
      document.documentElement.style.setProperty('--memoirCentralZone', scale);
    },

    onChangeLayoutSetting(layout) {
      this.updateLayout(layout);
    },
  });
});
