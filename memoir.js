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
        'commissarCard',
        'playCommissarCard',
        'playCard',
        'orderUnits',
        'moveUnits',
        'opponentAmbush',
        'ambushResolve',
        'battleBack',
        'drawChoice',
        'targetMedics',
        'targetAirPower',
        'targetBarrage',
        'orderUnitsFinestHour',
        'airDrop',
        'airDrop2',
        'confirmTurn',
        'targetBridge',
        'trainReinforcement',
        'reserveUnitsDeployement',
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
        ['moveUnitFromReserve',600],
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
        ['airDrop2', 500],
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
        ['updateStats', 1],
        ['removeStarToken', 1],
        ['removeUnit', 1],
        ['proposeScenario', 1],
        ['trainReinforcement', 500],
        ['reserveUnitsDeployement',1000],
        ['clearEndReserveDeployement',500],
      ];

      // Fix mobile viewport (remove CSS zoom)
      this.default_viewport = 'width=700';

      this._backCardIdCounter = -1; // Used to generate unique id for backCards
      this._boardTooltips = {}; // Used to store pending board tooltip element
    },

    getSettingsConfig() {
      return {
        confirmTurn: { type: 'pref', prefId: 103 },
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
        autoPass: { type: 'pref', prefId: 150, local: true },
        unitSprite: {
          default: 0,
          name: _('Units sprite'),
          attribute: 'unitSprite',
          type: 'select',
          values: {
            0: _('New sprite'),
            1: _('Old sprite (used on the OL)'),
          },
        },
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
      this.updateBottomTeam(this.gamedatas.players[this._pId].team);

      this.setupBoardButtonsTooltips();

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

    updateBottomTeam(team) {
      this._bottomTeam = this.gamedatas.players[this._pId].team;
      $('ebd-body').dataset.bottomTeam = this._bottomTeam;
    },

    clearInterface(partial = false) {
      this.closeAllTooltips();
      dojo.empty('m44-board-terrains');
      dojo.empty('m44-board-units');
      dojo.empty('m44-board-labels');

      dojo.empty('bottom-medals-container');
      dojo.empty('top-medals-container');

      if (!partial) {
        dojo.empty('scenario-informations');

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

        dojo.query('.player-panel-wrapper').forEach(dojo.destroy);

        dojo.empty('discard');
        dojo.destroy('scenario-dropzone-container');
        dojo.destroy('scenario-lobby');

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
        airDrop2: 'para',
        commissarCard: 'commissar',
        playCommissarCard: 'command',
        playCard: 'command',
        orderUnits: 'order',
        moveUnits: 'move',
        ambushResolve: 'move',
        attackUnits: 'attack',
        drawChoice: 'command',
        targetAirPower: 'attack',
        targetBarrage: 'attack',
        opponentAmbush: 'command',
        confirmTurn: 'command',
        targetBridge: 'order',
        blowBridge: 'attack',
        trainReinforcement: 'move',
        //reserveUnitsDeployement : 'command',
      };

      if (Object.keys(statusMapping).includes(stateName)) {
        let pId = this.getActivePlayerId();
        let team = this.gamedatas.players[pId].team;
        this.updateTeamStatus(team, statusMapping[stateName]);
      }

      // Add restart button
      if (args.possibleactions && args.possibleactions.includes('actRestart') && this.isCurrentPlayerActive()) {
        let actionCount = args.args && args.args.actionCount ? args.args.actionCount : 0;
        if (actionCount == 0) {
          return;
        }

        this.addDangerActionButton(
          'btnRestartTurn',
          _('Undo actions'),
          () => {
            this.takeAction('actRestart');
          },
          'restartAction',
        );
      }
    },

    notif_removeStarToken(n) {
      debug('Notif : removing star token of unit', n);
      $(`board-token-unit-${n.args.id}`).remove();
      this._grid[n.args.x][n.args.y].unit.equipment = false;
    },

    notif_removeUnit(n) {
      debug('Notif : removing unit like wagon when eliminated', n);
      $(`unit-${n.args.id}`).remove();
      //this._grid[n.args.x][n.args.y].unit.equipment = false;
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
      this.gamedatas.round = n.args.round;
      this.gamedatas.visibility = n.args.visibility;
      this.updateBottomTeam(this.gamedatas.players[this._pId].team);
      this._deckCounter.setValue(n.args.deckCount);

      this.setupBoard();
      this.setupScenario();
      this.setupPlayers();
      this.setupTeams();
      this.updateLayout(localStorage.getItem('memoirLayout'));
    },

    notif_smallRefreshInterface(n) {
      debug('Refreshing the interface because of an undo', n);
      this.clearInterface(true);

      // Update gamedatas
      this.gamedatas.players = n.args.players;
      this.gamedatas.board = n.args.board;
      this.gamedatas.teams = n.args.teams;
      this.updateBottomTeam(this.gamedatas.players[this._pId].team);
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

    onEnteringStateConfirmTurn(args) {
      this.addPrimaryActionButton('btnConfirmTurn', _('Confirm'), () => {
        this.stopActionTimer();
        this.takeAction('actConfirmTurn');
      });

      const OPTION_CONFIRM = 103;
      //  let n = args.previousEngineChoices;
      //  let timer = Math.min(10 + 2 * n, 20);
      let timer = 10;
      this.startActionTimer('btnConfirmTurn', timer, this.prefs[OPTION_CONFIRM].value);
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
        'forTrainReinforcement',
        'forReserveUnitDeploy',
        'attacked',
        'mayAttack',
      ].forEach((className) => {
        this.removeClassNameOfCells(className);
      });
      $('m44-board').classList.remove('displayLineOfSight', 'displayLineOfSightAttack');
      dojo.query('.choice').removeClass('choice');
      dojo.query('.dice-mini').forEach(dojo.destroy);
      dojo.query('.mustStop').forEach(dojo.destroy);
      dojo.query('.cannotAttack').forEach(dojo.destroy);
      if (this.gamedatas.isCampaign) {
        $('bottom-reserve-0').classList.remove('forReserveStagingDeploy');
        $('bottom-reserve-1').classList.remove('forReserveStagingDeploy');
      }
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

          if (args.coordSource) {
            args.coordSource = `<span class='log-coordinate'>${args.coordSource}</span>`;
          }
          if (args.coordTarget) {
            args.coordTarget = `<span class='log-coordinate'>${args.coordTarget}</span>`;
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
      if ($('popin_showScenarioLobby_container')) {
        $('popin_showScenarioLobby_container').remove();
      }

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
        title: _(this.getScenarioTexts().name ?? ''),
      });

      this.addTooltip('clipboard-button', _('Show the scenario informations'), '');
      $('clipboard-button').addEventListener('click', () => dial.show());
      this.updateGameProgress();
    },

    getScenarioTexts(scenario = null) {
      scenario = scenario || this.gamedatas.scenario;
      if (scenario.text.en) {
        return scenario.text.en;
      } else {
        let langs = Object.keys(scenario.text);
        return scenario.text[langs[0]];
      }
    },

    updateGameProgress() {
      $('scenario-name').innerHTML =
        _(this.getScenarioTexts().name) +
        '<span id="m44-progress">' +
        this.gamedatas.round +
        '/' +
        this.gamedatas.duration +
        '</span>';
    },

    notif_updateVisibility(n) {
      debug('Notif: visibility update', n);
      $('night-visibility').dataset.n = parseInt($('night-visibility').dataset.n) + n.args.star;
      if ($('night-visibility').dataset.n == 6) {
        setTimeout(() => dojo.destroy('night-visibility'), 800);
      }
    },

    tplScenarioModal(scenario = null, lobby = false) {
      scenario = scenario || this.gamedatas.scenario;
      let intervalFormat = new Intl.DateTimeFormat([], {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      });

      let begin = '';
      let end = '';
      // Compute start-end dates
      if (
        scenario.game_info.hasOwnProperty('date_begin') &&
        scenario.game_info.hasOwnProperty('date_end') &&
        scenario.game_info.date_begin !== null &&
        scenario.game_info.date_end !== null
      ) {
        let dateBegin = scenario.game_info.date_begin.split('-');
        let dateEnd = scenario.game_info.date_end.split('-');
        begin = new Date(Date.UTC(dateBegin[0], parseInt(dateBegin[1]) - 1, parseInt(dateBegin[2])));
        end = new Date(Date.UTC(dateEnd[0], parseInt(dateEnd[1]) - 1, parseInt(dateEnd[2])));
      }

      if (!lobby) {
        return (
          `
      <div id='scenario-dates'>
        ${intervalFormat.formatRange(begin, end)}
      </div>
      <div id='scenario-historical'>
        <h5>${_('Historical Background')}</h5>
        ${_(this.getScenarioTexts(scenario).historical ?? '').replace(/\n/g, '<br />')}
      </div>

      <div id='scenario-bottom-container'>
        <div id='scenario-brief'>
          <h5>${_('Briefing')}</h5>
          ${_(this.getScenarioTexts(scenario).description ?? '').replace(/\n/g, '<br />')}
        </div>
        <div id='scenario-conditions-rules'>
          <h5>${_('Conditions of Victory')}</h5>
          ${_(this.getScenarioTexts(scenario).victory ?? '').replace(/\n/g, '<br />')}
          ` +
          (this.getScenarioTexts(scenario).rules === undefined
            ? ''
            : `
          <h5>${_('Special rules')}</h5>
          ${_(this.getScenarioTexts(scenario).rules ?? '').replace(/\n/g, '<br />')}
            `) +
          `
        </div>
      </div>
    `
        );
      } else {
        return (
          `
        <div id='scenario-image-brief'>
          <img src='https://www.daysofwonder.com/memoir44/fr/memoire_board/?id=${
            scenario.id || scenario.meta_data.id
          }' />

          <div id='scenario-brief'>
            <h5>${_('Briefing')}</h5>
            <p>
              ${_(this.getScenarioTexts(scenario).description ?? '').replace(/\n/g, '<br />')}
            </p>
            <div id='lobby-button-container'></div>
          </div>
        </div>

          <div id='scenario-historical'>
            <div id='scenario-dates'>
              ${intervalFormat.formatRange(begin, end)}
            </div>
            <h5>${_('Historical Background')}</h5>
              ${_(this.getScenarioTexts(scenario).historical ?? '').replace(/\n/g, '<br />')}
          </div>

          <div id='scenario-conditions-rules'>
            <h5>${_('Conditions of Victory')}</h5>
            ${_(this.getScenarioTexts(scenario).victory ?? '').replace(/\n/g, '<br />')}
            ` +
          (this.getScenarioTexts(scenario).rules === undefined
            ? ''
            : `
            <h5>${_('Special rules')}</h5>
            ${_(this.getScenarioTexts(scenario).rules ?? '').replace(/\n/g, '<br />')}
              `) +
          `
        </div>
      `
        );
      }
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

    ////////////////////////////////////
    //  _          _     _
    // | |    ___ | |__ | |__  _   _
    // | |   / _ \| '_ \| '_ \| | | |
    // | |__| (_) | |_) | |_) | |_| |
    // |_____\___/|_.__/|_.__/ \__, |
    //                         |___/
    ////////////////////////////////////
    onEnteringStateLobbyFinalApprove(args) {
      this.onEnteringStateLobbyApproveProposeScenario(args, true);
    },

    onEnteringStateLobbyApproveProposeScenario(args, final = false) {
      if ($('lobby-button-container')) {
        $('lobby-button-container').innerHTML = '';
      }

      let showDetails = () => {
        var dial = new customgame.modal('showScenarioLobby', {
          class: 'memoir44_popin',
          closeIcon: 'fa-times',
          contents: this.tplScenarioModal(args.scenarioProposed, true),
          breakpoint: 800,
          autoShow: true,
          verticalAlign: 'flex-begin',
          scale: 0.8,
          title: _(this.getScenarioTexts(args.scenarioProposed).name ?? ''),
        });

        if (this.isCurrentPlayerActive()) {
          this.addPrimaryActionButton(
            `btnAccept`,
            _('Accept'),
            () => {
              this.takeAction('actValidateScenario', { accept: true });
            },
            $('lobby-button-container'),
          );

          if (!final) {
            // Propose another one
            $('lobby-button-container').insertAdjacentHTML(
              'beforeend',
              _('Or browse the scenarios to propose another one'),
            );
          } else {
            this.addDangerActionButton(
              `btnRefuse`,
              _('Refuse'),
              () => {
                this.takeAction('actValidateScenario', { accept: false });
              },
              $('lobby-button-container'),
            );
          }
        }
      };

      this.addPrimaryActionButton('showScenarioDetails', _('Show proposed scenario'), showDetails);
      if (this.isCurrentPlayerActive()) {
        showDetails();
      }

      this.onEnteringStateLobbyProposeScenario(args);
    },

    onEnteringStateLobbyProposeScenario(args) {
      this.clearInterface();
      dojo.place(
        `<div id="scenario-lobby">
          <div id="scenario-lobby-filters">
            <form id='form-lobby'>
              <div class='input-group'>
                ${_('ID:')}
                <input type='number' id='filter-id' />
              </div>

              <div class='input-group'>
                ${_('Front:')}
                <select id='filter-front'>
                  <option value=''>${_('All')}</option>
                  <option value='western'>${_('Western Front')}</option>
                  <option value='eastern'>${_('Eastern Front')}</option>
                  <option value='pacific'>${_('Pacific Theater')}</option>
                  <option value='mediterranean'>${_('Mediterranean Theater')}</option>
                </select>
              </div>

              <div class='input-group'>
                ${_('Name:')}
                <input type='text' id='filter-name' />
              </div>

              <div class='input-group'>
                ${_('Author:')}
                <input type='text' id='filter-author' />
              </div>

              <div class='input-group'>
                ${_('# per page')}
                <select id='filter-pagination'>
                  <option value='10'>10</option>
                  <option value='20'>20</option>
                  <option value='30'>30</option>
                  <option value='40'>40</option>
                  <option value='50'>50</option>
                </select>
              </div>

              <div class='input-group'>
                <button class='action-button bgabutton bgabutton_blue' type="submit">${_('GO')}</button>
              </div>
            </form>
          </div>
          <div id="scenario-lobby-list">
            <table>
              <thead>
                <tr>
                  <th><div><span id='sort-id-inc'>▼</span>${_('ID')}<span id='sort-id-desc'>▲</span></div></th>
                  <th><div><span id='sort-name-inc'>▼</span>${_('Title')}<span id='sort-name-desc'>▲</span></div></th>
                  <th><div><span id='sort-operation-inc'>▼</span>${_(
                    'Operation',
                  )}<span id='sort-operation-desc'>▲</span></div></th>
                  <th><div><span id='sort-front-inc'>▼</span>${_('Front')}<span id='sort-front-desc'>▲</span></div></th>
                  <th><div><span id='sort-author-inc'>▼</span>${_(
                    'Author',
                  )}<span id='sort-author-desc'>▲</span></div></th>
                </tr>
              </thead>
              <tbody id="scenario-lobby-table"></tbody>
            </table>
          </div>
          <div id="scenario-lobby-pagination"></div>
      </div>`,
        'm44-board-wrapper',
      );

      $('form-lobby').addEventListener('submit', (evt) => {
        evt.preventDefault();
        let query = this.lobbyGetFilters();
        query.order = this._lobbyCurrentQuery.order;
        this.lobbyFetchResult(query);
      });

      ['id', 'name', 'operation', 'front', 'author'].forEach((orderField) => {
        ['inc', 'desc'].forEach((order) => {
          this.onClick(`sort-${orderField}-${order}`, () => {
            this._lobbyCurrentQuery.order = [orderField, order];
            this._lobbyCurrentQuery.page = 1;
            this.lobbyFetchResult(this._lobbyCurrentQuery);
          });
        });
      });

      this.lobbyDisplayScenarioList(args.result);
    },

    lobbyGetFilters() {
      let filters = {
        front: $('filter-front').value,
        id: $('filter-id').value,
        name: $('filter-name').value,
        author: $('filter-author').value,
        pagination: $('filter-pagination').value,
      };
      for (let key in filters) {
        if (filters[key] === '') filters[key] = null;
      }
      return filters;
    },

    lobbyFetchResult(filters) {
      this.takeAction('actGetScenarios', { filters: JSON.stringify(filters), lock: false }).then((response) => {
        let data = response.data;
        this.lobbyDisplayScenarioList(data);
      });
    },

    lobbyDisplayScenarioList(result) {
      debug(result);
      this._lobbyCurrentQuery = result.query;

      // Make sure filters are kept synced
      ['id', 'front', 'name', 'author', 'pagination'].forEach((filter) => {
        $(`filter-${filter}`).value = result.query[filter] ?? '';
      });
      let o = $('scenario-lobby-list').querySelector('table thead tr th div span.active');
      if (o) {
        o.classList.remove('active');
      }
      $(`sort-${result.query.order[0]}-${result.query.order[1]}`).classList.add('active');

      // Scenario list
      $(`scenario-lobby-table`).innerHTML = '';
      if (result.scenarios.length) {
        result.scenarios.forEach((scenario) => {
          $('scenario-lobby-table').insertAdjacentHTML(
            'beforeend',
            `<tr id='scenario-${scenario.id}'>
            <td>${scenario.id}</td>
            <td id='scenario-name-${scenario.id}'>${_(scenario.name)}</td>
            <td>${scenario.game_info.operation.name ?? ''}</td>
            <td>${scenario.game_info.front}</td>
            <td>${scenario.meta_data.author.login ?? ''}</td>
          </tr>`,
          );

          this.addCustomTooltip(
            `scenario-name-${scenario.id}`,
            `<img src='https://www.daysofwonder.com/memoir44/fr/memoire_board/?id=${scenario.id}' width="386" height="272" />`,
          );

          this.onClick(`scenario-${scenario.id}`, () => {
            var dial = new customgame.modal('showScenarioLobby', {
              class: 'memoir44_popin',
              closeIcon: 'fa-times',
              contents: this.tplScenarioModal(scenario, true),
              breakpoint: 800,
              autoShow: true,
              verticalAlign: 'flex-begin',
              scale: 0.8,
              title: _(this.getScenarioTexts(scenario).name ?? ''),
            });

            if (this.isCurrentPlayerActive()) {
              this.addPrimaryActionButton(
                `btnProposeScenario${scenario.id}`,
                _('Propose'),
                () => {
                  this.takeAction('actProposeScenario', { id: scenario.id });
                },
                $('lobby-button-container'),
              );
            }
          });
        });
      } else {
        $('scenario-lobby-table').insertAdjacentHTML(
          'beforeend',
          `<tr><td colspan="5" style="text-align:center">${_('Sorry, no result could be found')}</td></tr>`,
        );
      }

      // Pagination
      $('scenario-lobby-pagination').innerHTML = '';
      $('scenario-lobby-pagination').insertAdjacentHTML(
        'beforeend',
        `${_('Pages')} (${result.numPages}) [<span id='lobby-pagination'></span>]`,
      );
      let currentPage = result.currentPage;

      if (currentPage != 1) {
        $('lobby-pagination').insertAdjacentHTML(
          'beforeend',
          "<span id='lobby-pagination-first'>«</span><span id='lobby-pagination-prev'>&lt;</span>",
        );
        this.onClick(`lobby-pagination-first`, () => {
          result.query.page = 1;
          this.lobbyFetchResult(result.query);
        });
        this.onClick(`lobby-pagination-prev`, () => {
          result.query.page = parseInt(currentPage) - 1;
          this.lobbyFetchResult(result.query);
        });
      }

      let minPageDisplayed = Math.max(1, currentPage - 5);
      let maxPageDisplayed = Math.min(result.numPages, minPageDisplayed + 10);
      for (let i = minPageDisplayed; i <= maxPageDisplayed; i++) {
        $('lobby-pagination').insertAdjacentHTML(
          'beforeend',
          `<span id='lobby-pagination-goto-${i}' class='${i == currentPage ? 'current' : ''}'>${i}</span>`,
        );
        let page = i;
        this.onClick(`lobby-pagination-goto-${i}`, () => {
          result.query.page = page;
          this.lobbyFetchResult(result.query);
        });
      }

      if (currentPage < result.numPages) {
        $('lobby-pagination').insertAdjacentHTML(
          'beforeend',
          "<span id='lobby-pagination-next'>&gt;</span><span id='lobby-pagination-last'>»</span>",
        );
        this.onClick(`lobby-pagination-last`, () => {
          result.query.page = result.numPages;
          this.lobbyFetchResult(result.query);
        });
        this.onClick(`lobby-pagination-next`, () => {
          result.query.page = parseInt(currentPage) + 1;
          this.lobbyFetchResult(result.query);
        });
      }
    },

    notif_proposeScenario(n) {
      debug('Notif: propose scenario', n);
      // TODO
    },

    onEnteringStateChangeOfRound(args) {
      if (this.isCurrentPlayerActive()) {
        this.openStatsModal();
        this.addPrimaryActionButton('btnProceed', _('Proceed to next round'), () => this.takeAction('actProceed'));
      }
    },

    onUpdateActivityChangeOfRound(args, status) {
      if (!status) {
        dojo.destroy('btnProceed');
      }
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
      let scoreBtn = $('show-scores');
      dojo.connect(scoreBtn, 'click', () => this.openStatsModal());
      this.addTooltip(scoreBtn, '', _('Show score details'));
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

    //////////////////////////////
    //  ____  _        _
    // / ___|| |_ __ _| |_ ___
    // \___ \| __/ _` | __/ __|
    //  ___) | || (_| | |_\__ \
    // |____/ \__\__,_|\__|___/
    //
    //////////////////////////////
    openStatsModal() {
      var modal = new customgame.modal('showStats', {
        autoShow: true,
        class: 'memoir44_popin',
        closeIcon: 'fa-times',
        openAnimation: true,
        openAnimationTarget: 'show-scores',
        contents: this.tplStatsModal(),
        breakpoint: 800,
        scale: 0.8,
        title: _('Game statistics'),
      });
    },

    tplStatsModal() {
      let players = this.gamedatas.players;
      let pIds = Object.keys(players);
      let pId1 = pIds[0],
        pId2 = pIds[1];

      let statsLabels = [
        [_('Played side'), 10, 30],
        [_('Result'), 11, 31],
        [_('Earned medals'), 12, 32],
        [_('Killed infantry units'), 13, 33],
        [_('Killed armor units'), 14, 34],
        [_('Killed artillery units'), 15, 35],
        [_('Killed infantry figures'), 16, 36],
        [_('Killed armor figures'), 17, 37],
        [_('Killed artillery figures'), 18, 38],
      ];

      let twoWays = this.gamedatas.duration == 2;
      let getStat = (pId, type) => {
        let v = this.gamedatas.stats.find((stat) => stat.type == type && stat.pId == pId);
        return v ? v.value : 0;
      };

      let showStatus = ['changeOfRound', 'gameEnd'].includes(this.gamedatas.gamestate.name);
      let teamNames = [_('Allies'), _('Axis')];
      let statusNames = [_('Loser'), _('Winner')];
      return (
        `<table id='stats-holder'>
        <thead>
          <tr>
            <th></th>
            <th colspan="${twoWays ? 3 : 1}" style="color:#${players[pId1].color}">${players[pId1].name}</th>
            <th colspan="${twoWays ? 3 : 1}" style="color:#${players[pId2].color}">${players[pId2].name}</th>
          </tr>
          ` +
        (twoWays
          ? `<tr>
            <th></th>
            <th>${_('Round 1')}</th>
            <th>${_('Round 2')}</th>
            <th>${_('Total')}</th>
            <th>${_('Round 1')}</th>
            <th>${_('Round 2')}</th>
            <th>${_('Total')}</th>
          </tr>`
          : '') +
        `
        </thead>
        <tbody>
        ` +
        statsLabels
          .map((stat) => {
            let cells = [`<th>${_(stat[0])}</th>`];
            pIds.forEach((pId) => {
              let v = getStat(pId, stat[1]);
              if (stat[1] == 10) v = teamNames[v];
              if (stat[1] == 11) {
                v = this.gamedatas.round == 1 && !showStatus ? '-' : statusNames[v];
              }

              cells.push(`<td>${v}</td>`);
              if (twoWays) {
                let v2 = getStat(pId, stat[2]);
                if (stat[1] == 10) v2 = teamNames[v2];
                if (stat[1] == 11) {
                  v2 = this.gamedatas.round <= 2 && !showStatus ? '-' : statusNames[v2];
                }
                cells.push(`<td>${this.gamedatas.round == 1 ? '-' : v2}</td>`);

                let total = parseInt(v) + parseInt(v2);
                if (stat[1] <= 11) total = '';
                cells.push(`<td>${total}</td>`);
              }
            });

            return '<tr>' + cells.join('') + '</tr>';
          })
          .join('') +
        `
        </tbody>
      </table>`
      );
    },

    notif_updateStats(n) {
      debug('Notif: update stats', n);
      this.gamedatas.stats = n.args.stats;
    },
  });
});
