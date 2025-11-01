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
        'armorBreakthroughDeployement',
        'smokescreen',
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
        ['flipSmokeScreen', 100],
        ['commissarCard', 1000],
        ['pCommissarCard', 1000],
        ['revealCommissarCard', 1000],
        ['exitUnit', 500],
        ['clearTurn', 1],
        ['smallRefreshInterface', 1],
        ['smallRefreshHand', 1],
        ['updateVisibility', 500],
        ['updateTurn', 1],
        ['updateStats', 1],
        ['updateCampaignScores',1],
        ['removeStarToken', 1],
        ['removeUnit', 1],
        ['proposeScenario', 1],
        ['trainReinforcement', 500],
        ['reserveUnitsDeployement',1000],
        ['clearEndReserveDeployement',500],
        ['addAirPowerToken',500],
        ['removeAirPowerToken',500],
        ['replenishWinnerReserveTokens',1],
        ['armorBreakthroughDeployement',1000],
        ['smokescreen',1000],
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
        armorBreakthrough: 'order',
        smokeScreen: 'order',
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
        'airPowerTarget',
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
        var elem0 = document.getElementById('bottom-reserve-0');
        if (elem0 != null) {
          $('bottom-reserve-0').classList.remove('forReserveStagingDeploy');
        }
        var elem1 = document.getElementById('bottom-reserve-1');
        if (elem1 != null) {
          $('bottom-reserve-1').classList.remove('forReserveStagingDeploy');
        }
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

      if (this.gamedatas.isCampaign) {
        console.log('create campaign, clipboard-button');
        dojo.place(
          `<div id='campaign-clipboard-button'>
  <svg xmlns="http://www.w3.org/2000/svg"
     viewBox="0 0 384 512">
  <path id="Sélection"
        fill="black" stroke="black" stroke-width="1"
        d="M 178.00,0.22
           C 155.79,4.53 135.44,13.49 120.43,31.00
             115.89,36.29 111.68,41.87 108.31,48.00
             106.43,51.42 102.61,61.28 99.78,62.98
             97.74,64.20 94.34,64.00 92.00,64.00
             92.00,64.00 45.00,64.00 45.00,64.00
             38.80,64.03 34.81,64.81 29.00,67.07
             12.79,73.36 0.21,90.51 0.00,108.00
             0.00,108.00 0.00,125.00 0.00,125.00
             0.00,125.00 0.00,156.00 0.00,156.00
             0.00,156.00 0.00,466.00 0.00,466.00
             0.04,490.04 19.48,511.71 44.00,512.00
             44.00,512.00 149.00,512.00 149.00,512.00
             149.00,512.00 338.00,512.00 338.00,512.00
             363.60,511.96 383.96,491.60 384.00,466.00
             384.00,466.00 384.00,323.00 384.00,323.00
             384.00,323.00 384.00,110.00 384.00,110.00
             383.96,84.40 363.60,64.04 338.00,64.00
             338.00,64.00 292.00,64.00 292.00,64.00
             289.66,64.00 286.26,64.20 284.22,62.98
             281.39,61.28 277.57,51.42 275.69,48.00
             272.05,41.38 265.34,32.32 259.96,27.09
             244.43,11.96 221.03,0.10 199.00,0.22
             199.00,0.22 178.00,0.22 178.00,0.22 Z
           M 186.00,65.30
           C 197.85,64.12 208.65,66.13 216.47,76.01
             230.54,93.80 221.54,123.22 198.00,126.70
             187.59,128.24 176.61,125.70 169.18,117.91
             163.58,112.03 161.09,105.00 161.00,97.00
             160.84,80.67 169.57,68.51 186.00,65.30 Z
           M 113.00,192.10
           C 113.00,192.10 165.00,192.10 165.00,192.10
             165.00,192.10 264.00,192.10 264.00,192.10
             269.14,192.01 275.50,192.23 279.98,195.01
             288.99,200.57 289.51,214.11 280.96,220.35
             276.31,223.74 269.54,223.99 264.00,224.00
             264.00,224.00 120.00,224.00 120.00,224.00
             113.51,223.99 106.10,223.68 101.21,218.79
             94.11,211.68 96.08,199.08 105.00,194.45
             107.48,193.17 110.29,192.75 113.00,192.10 Z
           M 181.00,312.00
           C 182.82,318.42 182.00,334.51 182.00,342.00
             182.00,342.00 182.00,399.00 182.00,399.00
             182.00,405.50 181.09,421.82 183.57,427.00
             185.18,430.36 187.10,431.87 190.00,434.00
             190.00,434.00 189.00,437.00 189.00,437.00
             199.35,429.74 195.47,450.55 195.40,450.69
             194.10,453.32 191.37,452.69 189.00,452.42
             184.55,451.91 171.45,451.70 170.00,447.00
             166.54,447.62 164.04,447.87 163.00,444.00
             155.66,444.60 142.42,434.44 142.00,427.00
             138.89,426.28 139.03,426.15 139.00,423.00
             134.35,421.92 132.51,416.08 130.81,412.00
             126.84,402.46 122.13,386.10 125.00,376.00
             121.22,374.65 123.50,363.47 123.83,360.00
             125.15,346.43 126.76,336.53 132.78,324.00
             134.80,319.80 137.14,314.08 142.00,313.00
             142.04,308.37 143.49,306.86 148.00,306.00
             148.00,306.00 150.00,304.00 150.00,304.00
             151.00,298.84 154.78,295.94 160.00,296.00
             161.61,289.00 179.41,284.58 186.00,283.84
             187.75,283.85 192.27,283.12 193.69,283.84
             196.89,284.97 196.73,297.01 194.98,299.44
             193.56,301.41 190.49,301.60 187.33,304.53
             184.91,306.77 183.97,311.04 181.00,312.00 Z
           M 250.00,287.00
           C 254.36,288.13 253.95,293.15 254.00,297.00
             254.00,297.00 254.00,328.00 254.00,328.00
             254.00,332.72 254.38,340.72 253.06,344.98
             251.05,351.49 242.42,353.33 241.00,348.00
             235.52,346.70 234.04,336.81 235.00,332.00
             230.92,330.78 229.63,324.79 230.00,321.00
             225.56,319.98 224.14,316.26 224.00,312.00
             224.00,312.00 214.00,305.64 214.00,305.64
             214.00,305.64 202.00,301.00 202.00,301.00
             202.00,301.00 202.00,284.00 202.00,284.00
             209.11,284.06 212.35,284.95 219.00,287.42
             221.80,288.47 229.36,291.50 232.00,291.53
             238.82,291.61 238.04,282.81 246.93,284.15
             249.40,284.52 249.44,284.91 250.00,287.00 Z
           M 244.00,428.00
           C 243.96,432.63 242.51,434.14 238.00,435.00
             236.43,443.24 220.01,451.88 212.00,450.00
             211.09,452.54 204.84,453.50 203.02,451.36
             201.24,449.27 201.44,437.81 203.60,436.01
             204.69,435.10 209.30,434.19 211.00,433.64
             213.90,432.71 216.49,430.75 219.00,433.00
             219.00,433.00 219.00,430.00 219.00,430.00
             219.00,430.00 225.00,429.00 225.00,429.00
             225.90,424.24 228.24,421.90 233.00,421.00
             232.80,416.56 233.90,415.85 235.68,412.00
             236.69,409.82 237.94,405.99 239.22,404.23
             242.52,399.69 249.99,399.98 252.69,405.06
             255.87,411.05 250.66,426.51 244.00,428.00 Z" />
             </svg>
          </div>`,
        'scenario-informations',
        );
      }

      if (this.gamedatas.visibility <= 6) {
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

      if (this.gamedatas.isCampaign) {
        console.log('setup campaign clipboard button');
        var dialCampaign = new customgame.modal('showCampaign', {
          class: 'memoir44_popin',
          closeIcon: 'fa-times',
          openAnimation: true,
          openAnimationTarget: 'campaign-clipboard-button',
          contents: this.tplCampaignModal(),
          breakpoint: 800,
          closeAction: 'hide',
          verticalAlign: 'flex-begin',
          scale: 0.8,
          title: _(this.getCampaignTexts().name ?? ''),
        });

        this.addTooltip('campaign-clipboard-button', _('Show the campaign informations'), '');
        $('campaign-clipboard-button').addEventListener('click', () => dialCampaign.show());
      }

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

    getCampaignTexts(campaign = null) {
      campaign = campaign || this.gamedatas.campaign;
      if (campaign.text.en) {
        return campaign.text.en;
      } else {
        let langs = Object.keys(campaign.text);
        return campaign.text[langs[0]];
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

    notif_updateTurn(n) {
      debug('Notif: Turn increase', n);
      this.gamedatas.turn = n.args.turn;
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
          <img src='https://api.asmodee.net/main/v1/m44/scenario/board/${
            scenario.id || scenario.meta_data.id
          }.webp?hires=2' width="50%" height="50%" />

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

    tplCampaignModal(campaign = null, lobby = false) {
      campaign = campaign || this.gamedatas.campaign;
      let intervalFormat = new Intl.DateTimeFormat([], {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      });

      let begin = '';
      let end = '';
      // Compute start-end dates
      if (
        campaign.game_info.hasOwnProperty('date_begin') &&
        campaign.game_info.hasOwnProperty('date_end') &&
        campaign.game_info.date_begin !== null &&
        campaign.game_info.date_end !== null
      ) {
        let dateBegin = campaign.game_info.date_begin.split('-');
        let dateEnd = campaign.game_info.date_end.split('-');
        begin = new Date(Date.UTC(dateBegin[0], parseInt(dateBegin[1]) - 1, parseInt(dateBegin[2])));
        end = new Date(Date.UTC(dateEnd[0], parseInt(dateEnd[1]) - 1, parseInt(dateEnd[2])));
      }

      const GENERALS_SPRITES = ['Montgomery', 'Rommel', 'Von_Rundstedt', 'Bradley', 'Von_Kluge'];
      player = this.player_id;
      team = this.gamedatas.players[player].team;
      side = team == 'ALLIES' ? 0 : 1;
      scenarios = campaign.scenarios[team];
      general = scenarios.general;
      general_briefing = scenarios.general_briefing;
      let generalsprite = 
      GENERALS_SPRITES.findIndex((t) => t == general);
      console.log('General', player, team, general, generalsprite);
      tokens_nbr = scenarios.reserve_tokens[0];
      token_container = ``;
      const NATION_SPRITES = ['GB', 'DE', 'US', 'FR'];
      sprite =
      NATION_SPRITES.findIndex((t) => t == scenarios.country);
      console.log(player.id, scenarios.country, sprite);
      if (tokens_nbr > 0) {
        token_container = `<div id='token-container'>`;
        
        for (let index = 0; index < tokens_nbr ; index++) {
          token_container += `<div class="reserve-token2" data-sprite="${sprite}"></div>`;      
        }
        token_container += `</div>`;
      }
      const CAMPAIGN_SCENARIOS = [4187, 4185, 4186, 1558];
      //scenario_name = campaign.scenarios.name[0];
      //scenario_id = campaign.scenarios.list[0];

      // iterate scenario numbers and display scenarios campaign container
      scenarios_nbr = campaign.scenarios.list.length;
      scenarios_container_tmp = ``;
      let round = this.gamedatas.round;

      for (let index = 0; index < scenarios_nbr; index++) {
        scenario_name = campaign.scenarios.name[index];
        next_scenario_name = campaign.scenarios.name[index + 1] ?? 'END';
        scenario_id = campaign.scenarios.list[index];
        column = index % 2 == 0 ? 'left' : 'right';
        color_win = team == 'ALLIES' ? '#5e6d3a' : '#3e5d75';
        scenario_sprite = CAMPAIGN_SCENARIOS.findIndex((t) => t == scenario_id);
        scenario_win_message = scenarios.win_message[index] ?? '';
        arrow_fill_opacity = index % 2 == 0 ? 1 : 1;
        players = this.gamedatas.players;
        pIds = Object.keys(players);
        pId_allies = players[pIds[0]].team == 'ALLIES' ? pIds[0] : pIds[1];
        pId_axis = players[pIds[0]].team == 'AXIS' ? pIds[0] : pIds[1];
        score_allies = campaign.scenarios['ALLIES'].score[round][index];
        score_axis = campaign.scenarios['AXIS'].score[round][index];
        arrow = index % 2 == 0 ? 
          `<svg xmlns="http://www.w3.org/2000/svg"
            width="257px"
            height="66px"
            viewBox="0 0 257 66">
            <path
              d="M 11.00,0.00 C 11.00,0.00 11.00,41.00 11.00,41.00 11.00,41.00 0.00,38.00 0.00,38.00 0.00,38.00 17.00,66.00 17.00,66.00 17.00,66.00 19.00,66.00 19.00,66.00 19.00,66.00 33.00,38.00 33.00,38.00 33.00,38.00 23.00,41.00 23.00,41.00 23.00,41.00 23.00,12.00 23.00,12.00 23.00,12.00 257.00,12.00 257.00,12.00 257.00,12.00 257.00,0.00 257.00,0.00 257.00,0.00 11.00,0.00 11.00,0.00 Z"
              style="fill:#000000;fill-opacity:${arrow_fill_opacity};stroke:none"
            />
          </svg>` :
          `<svg xmlns="http://www.w3.org/2000/svg"
            width="257px"
            height="66px"
            viewBox="0 0 257 66">
            <path
              d="m 0,0 c 0,0 246,0 246,0 0,0 0,42 0,42 0,0 10.5,-3.5 10.5,-3.5 0,0 -17.5,27 -17.5,27 0,0 -15,-27 -15,-27 0,0 10,3.5 10,3.5 0,0 0,-30 0,-30 C 234,12 0,12 0,12 0,12 0,0 0,0"
              style="fill:#000000;fill-opacity:${arrow_fill_opacity};stroke:none"
            />
          </svg>`;
        if (index == 0) {
          arrow = ``;          
        }
        next_scenario_banner = scenarios[index] == 'END' ? 
          `<svg xmlns="http://www.w3.org/2000/svg"
            width="401px"
            height="28px"
            viewBox="0 0 401 28">
            <path
              d="m 0.68723424,0.68723424 c 0,0 398.99999576,0 398.99999576,0 0,0 -0.5,15.49999976 -0.5,15.49999976 -104,10.5 -288,16 -398.49999576,1.5 0,0 0,-16.99999976 0,-16.99999976 M -8.8127658,25.687234"
              style="fill:#be683c;fill-opacity:1;stroke:none"
            />
            <text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" fill="white" font-size="20" font-family="Gunplay">END CAMPAIGN</text>
          </svg>`
        :
          `<svg xmlns="http://www.w3.org/2000/svg"
            width="401px"
            height="31px"
            viewBox="0 0 401 31">
            <path
              d="M 0.00,0.00            C 0.00,0.00 0.00,31.00 0.00,31.00              0.00,31.00 401.00,31.00 401.00,31.00              401.00,31.00 401.00,0.00 401.00,0.00              401.00,0.00 0.00,0.00 0.00,0.00 Z            M 389.00,15.00            C 389.00,15.00 379.00,22.00 379.00,22.00              379.00,22.00 378.00,6.00 378.00,6.00              383.22,7.73 386.52,10.00 389.00,15.00 Z            M 19.00,16.00            C 19.00,16.00 9.00,23.00 9.00,23.00              9.00,23.00 8.00,7.00 8.00,7.00              13.22,8.73 16.52,11.00 19.00,16.00 Z"
              style="fill:#f2e7d5;fill-opacity:1;stroke-width:0;stroke-miterlimit:4;stroke-dasharray:none"
            />
            <text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" fill="#623b2d" font-size="20" font-family="Gunplay">Play ${next_scenario_name} next</text>
          </svg>`;
        
        scenarios_container_tmp +=
            `<div id="scenarios-container-${column}">
              <div class="arrow-container-${column}">
                ${arrow}
              </div>
              <div class="campaign-step-titles" data-sprite="${sprite}">
                <div class="scenario-step-number-container">
                  <div class="campaign-step-number">
                  ${index + 1}
                  </div>
                </div>
                <div class="scenario-title-container">
                  <div class="scenario-title">
                  ${scenario_name}
                  </div>
                </div>
                <div class="scenario-id-container">
                  <div class="scenario-id">
                  ${scenario_id}
                  </div>
                </div>
              </div>
              <div class="scenario-box">
                <div class="scenario-box-top">
                  <div class="scenario-box-left">
                    <div>
                      <svg xmlns="http://www.w3.org/2000/svg"
                        width="69.5mm"
                        height="5.6600003mm"
                        viewBox="0 0 68.338989 5.6600003">
                        <path
                          style="opacity:1;fill:${color_win};fill-opacity:1;stroke:${color_win};stroke-width:0;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1"
                          d="M 2.205061,5.5412335 C 1.4621395,5.3220997 0.65405828,4.7387922 0.26152099,4.1383051 0.05003004,3.8147752 7.1765715e-4,3.570425 1.4733755e-5,2.8424843 -8.4040131e-4,2.0431644 0.03530628,1.8946805 0.3308658,1.4802735 0.51331116,1.2244661 0.88699437,0.85684432 1.1612747,0.66333594 2.1807117,-0.05589064 -0.73631358,0.00164703 34.707419,0.00164703 c 35.809346,0 32.547396,-0.0730457 33.663198,0.75382812 C 70.157855,2.0799152 69.766666,4.4516938 67.60167,5.4176524 L 67.117928,5.6334825 34.888821,5.6544103 C 8.1445523,5.6717792 2.5823375,5.6525456 2.205061,5.5412335 Z"
                        />
                        <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="white" font-size="3">${team} Win !</text>
                      </svg>
                    </div>
                    <div class="win-message-container">
                      <div class="campaign-medal" data-sprite="${sprite}">
                      </div>
                      <div class="win-message">
                      ${scenario_win_message.replace(/\n/g, '<br />')}
                      </div>
                    </div>
                  </div>
                  <div class="scenario-box-right">
                    <div class="scenario-map-mini" data-sprite="${scenario_sprite}">
                    </div>
                    <div class="scenario-scores">
                      <div class="scores-allies">
                        ${score_allies}
                      </div>
                      <div class="scores-axis">
                        ${score_axis}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="next-scenario-container">
                  ${next_scenario_banner}
                </div>
              </div>
              
            </div>`;
            
      }
      scenarios_container = `
      <div id='scenarios-container'>
        ${scenarios_container_tmp}
      </div>
      `;

      
      nbr_medals = campaign.scenarios[team].score[round].total;
      nbr_objectives = campaign.scenarios[team].score[round].objectives_medals;
      objectives_track_bonus = campaign.scenarios[team].score[round].objectives_bonus;
      victory_points = campaign.scenarios[team].score[round].victory_points;

      objectives_table_points = scenarios.objectives_points;
      objectives_table_length = objectives_table_points.length;
      td_objective_tmp = `<td width="25px">none</td>`;
      td_points_tmp = `<td width="25px">${objectives_table_points[0]}</td>`;
      for (let index = 1; index < objectives_table_length; index++) {
        if (index <= nbr_objectives) {
          td_objective_tmp += `<td width="25px">&#x2713;</td>`;
        } else {
          td_objective_tmp += `<td width="25px">    </td>`;
        }
        td_points_tmp += `<td width="25px">${objectives_table_points[index]}</td>`;
      }

      objective_track_table = `
      <table id='objective_track_table'>
        <colgroup>
          <col border: 2px solid black>
          <col span="2">
        </colgroup>
        <tbody>
          <tr>
            <td width="105px">Objectives</td>
            ${td_objective_tmp}
          </tr>
          <tr>
            <td>Points</td>
            ${td_points_tmp}
          </tr>
        </tbody>
      </table>
      `;
      

      if (!lobby) {
        return (
          `          
          <div id='operation_subtitle'>
          ${_(this.getCampaignTexts(campaign).subtitle ?? '').replace(/\n/g, '<br />')}
          </div>

          <div id='scenario-dates'>
          ${intervalFormat.formatRange(begin, end)}
          </div>

          <div id='general-briefing'>
            <div id='general' class='generals' data-sprite="${generalsprite}"> </div>
            <div id='general-speech'>
              ${general_briefing.replace(/\n/g, '<br />')}
            </div>
            <div id='reserve-tokens-panel' class='token-panel' data-sprite="${side}">
              <div id='start_with' class='token-panel-text'>
                Start with
              </div>
              <div id='start_with' class='token-panel-number'>
                ${tokens_nbr}
              </div>
              <div id='reserve_token_text' class='token-panel-text'>
                  Reserve Token
              </div>
              ${token_container}
            </div>
          </div>
          ${scenarios_container}
          <div id="bottom_tables">
            <div class="bottom-container">
              ${objective_track_table}
              <svg xmlns="http://www.w3.org/2000/svg"
                id="objective_table_svg"
                width="125px"
                height="25px"
                viewBox="0 0 125 25">
                <path
                  style="stroke:none;stroke-opacity:1;fill:#7b5442;fill-opacity:1"
                  d="M 1.00,0.00 C 1.00,0.00 124.00,0.00 124.00,0.00 126.00,16.00 118.18,25.10 104.00,25.00 104.00,25.00 23.00,24.50 23.00,24.50 0.00,25.00 1.50,12.50 1.00,-0.50"
                />
                <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="white" font-size="10">Objective Track</text>
              </svg>
            </div>
            <div class="bottom-container2">
              <table id="nbr_medals">
                <colgroup>
                  <col border: 2px solid black>
                  <col span="1">
                </colgroup>
                <thead>
                  <th scope="col" width="90px">MEDALS</th> 
                </thead>
                <tbody>
                  <tr>
                    <td>${nbr_medals}</td>
                  </tr>
                </tbody>
              </table>
              <div class="small-campaign-medal" data-sprite="${sprite}"></div>
              <div class="dot">+</div>
              <table id="nbr_objectives">
                <colgroup>
                  <col border: 2px solid black>
                  <col span="1">
                </colgroup>
                <thead>
                  <th scope="col" width="90px">OBJ. TRACK</th> 
                </thead>
                <tbody>
                  <tr>
                    <td>${objectives_track_bonus}</td>
                  </tr>
                </tbody>
              </table>
              <div class="dot2">=</div>
              <table id="victory_points">
                <colgroup>
                  <col border: 2px solid black>
                  <col span="1">
                </colgroup>
                <thead>
                  <th scope="col" width="90px">VICTORY POINTS</th> 
                </thead>
                <tbody>
                  <tr>
                    <td>${victory_points}</td>
                  </tr>
                </tbody>
              </table>
            </div>
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
            `<img src='https://api.asmodee.net/main/v1/m44/scenario/board/${scenario.id}.webp?hires=2' width="386" height="272" />`,
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

    notif_updateCampaignScores(n) {
      debug('Notif: update campaign score', n);
      this.gamedatas.campaign = n.args.campaign;
    },
  });
});
