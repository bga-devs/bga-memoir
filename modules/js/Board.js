define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
  // prettier-ignore
  const TERRAINS = ['airfield','airfieldX','barracks','bled','buildings','cairfield','camp','cemetery','church','coastcurve','coast','cravine','curve','dairfield','dairfieldX','dam','dcamp','depot','descarpment','dhill','dridge','droadcurve','droadFL','droadFR','droad','droadX','factory','fortress','hedgerow','highground','hillcurve','hillroad','hills','lakeA','lakeB','lakeC','lighthouse','marshes','mountain','oasis','pairfield','pairfieldX','palmtrees','pbeach','pcave','pheadquarter','phospital','pjungle','pmcave','pmouth','pond','powerplant','ppier','price','ptrenches','pvillage','radar','railcurve','railFL','railFR','rail','railroad','railX','ravine','riverFL','riverFR','river','riverY','roadcurve','roadFL','roadFR','road','roadX','roadY','station','wadi','wairfield','wcastle','wchurch','wcurved','wcurve','wfactory','wforest','whillforest','whill','whillvillage','wmarshes','woods','wrailcurve','wrailFR','wrail','wrailroad','wravine','wriverFR','wriver','wroadcurve','wroadFL','wroadFR','wroad','wroadX','wroadY','wruins','wtrenches','wvillage'];
  // prettier-ignore
  const TERRAINS_ROTATIONS = {
    3 : ['wadi','ptrenches','ravine','wtrenches','wravine','river','wriver','road','roadX','hillroad','droad','droadX','wroad','wroadX','airfield','airfieldX','pairfield','pairfieldX','cairfield','wairfield','dairfieldX','dairfield','rail','railX','station','railroad','wrail','wrailroad'],
    6 : ['wcurve','cravine','curve','riverFL','riverFR','pond','dam','pmouth','lakeA','lakeB','lakeC','wriverFR','wcurved','coast','coastcurve','roadcurve','roadFL','roadFR','hillcurve','droadcurve','droadFL','droadFR','wroadcurve','wroadFL','wroadFR','railcurve','railFL','railFR','wrailcurve','wrailFR'],
    2 : ['riverY','roadY','wroadY'],
  };

  // prettier-ignore
  const OBSTACLES = ['abatis','barge','bridge','brkbridge','bunker','casemate','dbunker','dragonteeth','droadblock','ford','hedgehog','loco','pbridge','pbunker','pcarrier','pdestroyer','pontoon','railbridge','roadblock','sand','wagon','wbridge','wbunker','wire','wpontoon','wrailbridge','wroadblock'];
  // prettier-ignore
  const OBSTACLES_ROTATION = { bunker: 180,wbunker : 180,dbunker : 180,ford : 60,roadblock : 60,droadblock : 60,wroadblock : 60,pontoon : -30,wpontoon : -30,dragonteeth : 60,railbridge : -60,wrailbridge : -60,bridge : -30,pbridge : -30,brkbridge : -30,wbridge : -30,abatis : 60,wire : 180,sand : 180};
  // prettier-ignore
  const UNITS_ROTATION = { 7 : -60, 6 : 60};

  const ALLIES_NATIONS = ['brit', 'us', 'ru'];

  const TOKEN_MEDAL = 1;
  const TOKEN_MINE = 2;
  const TOKEN_CAMOUFLAGE = 4;
  const TOKEN_EXIT_MARKER = 5;
  const TOKEN_ON_TOP = ['target'];

  function computeCoords(x, y) {
    // TODO : replace 9 by dim.y
    return String.fromCharCode(65 + (x % 2 == 0 ? 0 : 32) + parseInt(x / 2)) + (9 - y);
  }

  const isObject = (obj) => {
    return Object.prototype.toString.call(obj) === '[object Object]';
  };

  return declare('memoir.board', null, {
    setupBoard() {
      this._grid = [];
      let board = this.gamedatas.board;
      let rotate = this.gamedatas.teams.find((team) => team.team == this._bottomTeam).position == 1;
      this._isRotated = rotate;

      // Get dimensions based on type
      let type = board.type.toLowerCase();
      $('m44-board').dataset.type = type;
      $('ebd-body').dataset.deckMode = type;

      let dimensions = {
        standard: { x: 13, y: 9 },
        overlord: { x: 26, y: 9 },
        brkthru: { x: 13, y: 17 },
      };
      let dim = dimensions[type];

      let face = board.face.toLowerCase();

      // Create coordinates marker
      for (let y = 0; y < dim.y; y++) {
        let realY = !rotate ? dim.y - y - 1 : y;

        // Left
        let cellC = this.place('tplBoardCoordinateMarker', { mark: y + 1 }, 'm44-board-terrains');
        cellC.style.gridRow = 3 * realY + 4 + ' / span 2';
        cellC.style.gridColumn = '1 / span 1';
        //        cellC.style.gridColumn = realX + 1 + ' / span ' + (y % 2 == 0 ? 1 : 2);

        // Right
        cellC = this.place('tplBoardCoordinateMarker', { mark: y + 1 }, 'm44-board-terrains');
        cellC.style.gridRow = 3 * realY + 4 + ' / span 2';
        cellC.style.gridColumn = 2 * dim.x + 2 + ' / span 1';
      }
      for (let x = 0; x < 2 * dim.x - 1; x++) {
        let realX = rotate ? 2 * (dim.x - 1) - x : x;

        // Top
        let cellC = this.place(
          'tplBoardCoordinateMarker',
          { mark: String.fromCharCode(65 + (x % 2 == 1 ? 32 : 0) + parseInt(x / 2)) },
          'm44-board-terrains',
        );
        cellC.style.gridRow = (x % 2 == 0 ? 1 : 3 * dim.y + 4) + ' / span 2';
        //        cellC.style.gridRow = 1 + ' / span 2';
        //        cellC.style.gridRow = (x % 2 == 0 ? 1 : 2) + ' / span 2';
        cellC.style.gridColumn = realX + 2 + ' / span 2';
      }

      // Create cells
      for (let y = 0; y < dim.y; y++) {
        let size = dim.x - (y % 2 == 0 ? 0 : 1);
        for (let x = 0; x < size; x++) {
          // Compute coresponding col (2 scale on x-axis)
          let col = 2 * x + (y % 2 == 0 ? 0 : 1);
          let row = y;

          // Create node in the internal grid
          if (!this._grid[col]) this._grid[col] = [];
          this._grid[col][row] = {
            terrains: [],
            tokens: [],
            unit: null,
          };

          // Take into account rotation
          let realX = rotate ? 2 * size - col - (y % 2 == 0 ? 2 : 0) : col;
          let realY = rotate ? dim.y - y - 1 : y;

          // Background and terrains
          let tile = this.getBackgroundTile(face, dim, x, y);
          let cellC = this.place('tplBoardBackgroundCell', { x: col, y, tile, rotate }, 'm44-board-terrains');
          cellC.style.gridRow = 3 * realY + 3 + ' / span 4';
          cellC.style.gridColumn = realX + 2 + ' / span 2';
          board.grid[col][row].terrains.forEach((terrain) => {
            let tplName = OBSTACLES.includes(terrain.tile) ? 'tplObstacleTile' : 'tplTerrainTile';
            terrain.rotate = rotate;
            this._grid[col][row].terrains.push(terrain);
            this.place(tplName, terrain, cellC);
          });

          // Add labels
          let labels = board.grid[col][row].labels;
          if (labels.length > 0) {
            let label = labels.map((t) => _(t)).join('<br />');
            let area = 3 * realY + 4 + ' / ' + (+realX + 1) + ' / auto / span 2';
            this.place('tplTileLabel', { label, area }, 'm44-board-labels');
          }

          // Units
          let cell = this.place('tplBoardCell', { x: col, y }, 'm44-board-units');
          cell.style.gridRow = 3 * realY + 3 + ' / span 4';
          cell.style.gridColumn = realX + 2 + ' / span 2';

          let unit = board.grid[col][row].unit;
          if (unit) {
            unit.rotate = rotate;
            this._grid[col][row].unit = unit;
            this.place('tplUnit', unit, `cell-${col}-${y}`);
          }

          // Tokens/medals
          board.grid[col][row].tokens.forEach((token) => this.addToken(token));

          // Add tooltip listeners
          cell.addEventListener('mouseenter', (evt) => {
            if (this._summaryHover == 1 && !this.isMobile()) {
              this.openBoardTooltip(col, row, evt.clientX);
            }
          });
          cell.addEventListener('mouseleave', () => {
            if (this._summaryHover == 1 && !this.isMobile()) {
              this.closeBoardTooltip(col, row);
            }
          });
          cell.addEventListener('click', (evt) => {
            if (this._summaryClick == 1 && this.openBoardTooltipModal(col, row)) {
              evt.stopPropagation();
              evt.stopImmediatePropagation();
              return false;
            }
          });
        }
      }

      // Add sections dividers
      let dividers = {
        standard: [9, 19],
        overlord: [9, 19, 27, 35, 45],
        brkthru: [9, 19],
      };
      dividers[type].forEach((x) => {
        let o = this.place('tplBoardDivider', {}, 'm44-board-terrains');
        o.style.gridRow = '3 / span ' + (3 * dim.y + 1);
        o.style.gridColumn = x + 1 + ' / span 1';
      });

      // Add line of sight and dice container
      dojo.place('<div id="lineOfSight"></div>', 'm44-board-units');
      dojo.place('<div id="diceContainer"></div>', 'm44-board-units');
      dojo.place('<div id="explosionContainer"></div>', 'm44-board-units');
    },

    setupBoardButtonsTooltips() {
      if (this.tooltips['m44-labels-settings'] == undefined) {
        let buttonsTooltips = {
          opponentUnits: _("Show/hide opponents' units"),
          terrains: _('Show/hide the terrain hexes'),
          tokens: _('Show/hide tokens and medals'),
          labels: _('Show/hide map labels'),
          coords: _('Show/hide coordinate helpers'),
          ownUnits: _('Show/hide my units'),
          summaryHover: _('Enable/disable summary cards when hover on hexes'),
          summaryClick: _(
            "Enable/disable summary cards when cliking on a hex. Warning: enabling this implies you won't be able to click on your units, don't forget to disable it once you are done examinating the board.",
          ),
        };

        ['opponentUnits', 'terrains', 'tokens', 'labels', 'coords', 'ownUnits'].forEach((layer) => {
          let name = '_' + layer + 'Visibility';
          this.toggleBoardSettings(layer, name, this.getConfig('m44' + layer, 1));
          dojo.connect($(`m44-${layer}-settings`), 'click', () => this.toggleBoardSettings(layer, name));
          this.addTooltip(`m44-${layer}-settings`, '', buttonsTooltips[layer]);
        });

        ['summaryHover', 'summaryClick'].forEach((setting) => {
          let name = '_' + setting;
          this.toggleBoardSettings(setting, name, this.getConfig('m44' + setting, setting == 'summaryClick' ? 0 : 1));
          dojo.connect($(`m44-${setting}-settings`), 'click', () => this.toggleBoardSettings(setting, name));
          this.addTooltip(`m44-${setting}-settings`, '', buttonsTooltips[setting]);
        });

        dojo.connect($('m44-summary-settings-showAll'), 'click', () => this.openAllTooltipModal());
        this.addTooltip('m44-summary-settings-showAll', '', _('Show all the summary cards relevant to this scenario'));

        dojo.connect($('m44-react-settings'), 'click', () => {
          this.setPreferenceValue(150, 1 - this.prefs[150].value);
        });
        this.addTooltip(
          `m44-react-settings`,
          '',
          _('Enable/disable auto-pass for reacting to close combat without any ambush card in hand'),
        );
      }
    },

    removeClassNameOfCells(className) {
      let cells = [...$('m44-board').getElementsByClassName(className)];
      cells.forEach((cell) => cell.classList.remove(className));
    },

    toggleBoardSettings(attribute, name, value = null) {
      if (value == null) {
        this[name] = 1 - this[name];
      } else {
        this[name] = value;
      }

      $('m44-board-wrapper').dataset[attribute] = this[name];
      localStorage.setItem('m44' + attribute, this[name]);
    },

    getBackgroundTile(face, dim, x, y) {
      let tile = 0;
      if (face == 'winter') {
        tile = 30 + (x % 6);
      } else if (face == 'desert') {
        tile = 19 + (x % 6);
      } else if (face == 'country') {
        tile = 9 + (x % 4);
      } else if (face == 'beach') {
        let b = dim.y - y;
        if (dim.y == 17) b -= 1;
        if (b < 2) tile = 27 + (x % 2);
        else if (b < 3) tile = 25 + (x % 2);
        else if (b < 4) tile = 6 + (x % 2);
        else if (b < 5) tile = 4 + (x % 2);
        else if (b < 6) tile = 2 + (x % 2);
        else if (b < 7) tile = 0 + (x % 2);
        else tile = 9 + (x % 4);
      }
      return tile;
    },

    tplBoardCoordinateMarker(cell) {
      return `<li class="hex-grid-item coordinate-marker">${cell.mark}</li>`;
    },

    tplBoardBackgroundCell(cell) {
      let rotation = cell.rotate ? 6 : 0;
      return `<li class="hex-grid-item" id="cell-background-${cell.x}-${cell.y}">
      <div class="hex-grid-content hex-grid-background" data-tile="${cell.tile}" data-rotation="${rotation}"></div>
    </li>`;
    },

    tplBoardCell(cell) {
      return `<li class="hex-grid-item hex-cell-container" id="cell-container-${cell.x}-${cell.y}">
      <div class='hex-cell' id="cell-${cell.x}-${cell.y}"></div>
    </li>`;
    },

    tplBoardDivider() {
      return '<li class="board-divider"></li>';
    },

    tplTileLabel(label) {
      return `
      <div class="hex-label-container" style="grid-area:${label.area}">
        <div class="hex-label">${label.label}</div>
      </div>`;
    },

    ////////////////////////////////////////////
    //  _____                   _
    // |_   _|__ _ __ _ __ __ _(_)_ __  ___
    //   | |/ _ \ '__| '__/ _` | | '_ \/ __|
    //   | |  __/ |  | | | (_| | | | | \__ \
    //   |_|\___|_|  |_|  \__,_|_|_| |_|___/
    ////////////////////////////////////////////

    notif_addTerrain(n) {
      debug('Notif: adding obstacle', n);
      let terrain = n.args.terrain;
      let cellC = $(`cell-background-${terrain.x}-${terrain.y}`);
      terrain.rotate = this._isRotated;
      if (terrain.tile == 'hills' || terrain.tile == 'mountain') {
        this.place('tplTerrainTile', terrain, cellC);
      } else {
        this.place('tplObstacleTile', terrain, cellC);
      }
      this._grid[terrain.x][terrain.y].terrains.push(terrain);
    },

    notif_removeTerrain(n) {
      debug('Notif: removing obstacle', n);
      $('terrain-' + n.args.terrainId).remove();
      let x = n.args.cell.x,
        y = n.args.cell.y;
      this._grid[x][y].terrains = this._grid[x][y].terrains.filter((terrain) => terrain.id != n.args.terrainId);
    },

    notif_revealMinefield(n) {
      debug('Notif: revealing minefiled', n);
      if (n.args.value > 0) {
        let tile = `mine${n.args.value}`;
        $(`terrain-${n.args.terrainId}`).dataset.tile = tile;
        this._grid[n.args.cell.x][n.args.cell.y].terrains.forEach((terrain) => {
          if (terrain.id == n.args.terrainId) {
            terrain.tile = tile;
          }
        });
      }
    },

    tplTerrainTile(terrain) {
      let className = '';
      let tile = TERRAINS.findIndex((t) => t == terrain.tile);
      if (terrain.tile.substr(0, 10) == 'background') {
        // Special case of background terrains as beaches or flooded ground
        tile = terrain.tile.substr(11);
        className = 'background-terrain';
      }
      if (terrain.tile.substr(0, 4) == 'mine') {
        // Special case of minefields
        tile = terrain.tile;
      }

      let rotation = terrain.rotate ? 6 : 0;
      if (terrain.orientation != 1) {
        let nbrRotation = 3;
        if (TERRAINS_ROTATIONS[6].includes(terrain.tile)) nbrRotation = -6;
        if (TERRAINS_ROTATIONS[2].includes(terrain.tile)) nbrRotation = 2;
        rotation += ((terrain.orientation - 1) * 12) / nbrRotation + 12;
      }
      rotation = rotation % 12;
      return `<div id="terrain-${terrain.id}" class="hex-grid-content hex-grid-terrain ${className}" data-tile="${tile}" data-rotation="${rotation}"></div>`;
    },

    tplObstacleTile(obstacle) {
      let tile = OBSTACLES.findIndex((t) => t == obstacle.tile);
      let rotation = obstacle.rotate ? 6 : 0;
      if (obstacle.orientation != 1) {
        let angle = OBSTACLES_ROTATION[obstacle.tile] / -30;
        rotation += (obstacle.orientation - 1) * angle + 12;
      }
      rotation = rotation % 12;
      return `<div id="terrain-${obstacle.id}" class="hex-grid-content hex-grid-obstacle" data-tile="${tile}" data-rotation="${rotation}"></div>`;
    },

    ////////////////////////////////////////
    //  _____     _
    // |_   _|__ | | _____ _ __  ___
    //   | |/ _ \| |/ / _ \ '_ \/ __|
    //   | | (_) |   <  __/ | | \__ \
    //   |_|\___/|_|\_\___|_| |_|___/
    ////////////////////////////////////////

    addToken(token) {
      let tplName = token.type == TOKEN_MEDAL ? 'tplBoardMedal' : 'tplBoardToken';
      let onTop = TOKEN_ON_TOP.includes(token.sprite);
      let container = `cell${onTop ? '' : '-background'}-${token.x}-${token.y}`;
      this.place(tplName, token, container);
      this._grid[token.x][token.y].tokens.push(token);
    },

    tplBoardToken(token) {
      // prettier-ignore
      const SPRITES = ['star', 'tag4', 'tag5', 'mine0', 'mine1', 'mine2', 'mine3', 'mine4', 'mineX', 'tag14', 'tag15', 'target'];
      let sprite = SPRITES.findIndex((t) => t == token.sprite);

      return `<div id='board-token-${token.id}' class="board-token" data-sprite="${sprite}"></div>`;
    },

    tplBoardMedal(medal) {
      const SPRITES = ['medal1', 'medal2', 'medal4', 'medal5', 'medal6', 'medal7', 'medal8', 'medal9'];
      let sprite = SPRITES.findIndex((t) => t == medal.sprite);
      if (medal.sprite == 'medal0') {
        sprite = 'both';
      }

      return `
      <div id='board-medal-${medal.id}' class="board-medal"
        data-team="${medal.team}" data-sprite="${sprite}" data-permanent="${medal.datas.permanent ? 1 : 0}"></div>`;
    },

    notif_addToken(n) {
      debug('Notif: a token is added on the board', n);
      this.addToken(n.args.token);
    },

    notif_removeToken(n) {
      debug('Notif: a token is removed from the board', n);
      let token = n.args.token;
      $(`board-token-${token.id}`).remove();
      this._grid[token.x][token.y].tokens = this._grid[token.x][token.y].tokens.filter((t) => t.id != token.id);
    },

    ////////////////////////////////////////
    //  _____           _ _   _
    // |_   _|__   ___ | | |_(_)_ __
    //   | |/ _ \ / _ \| | __| | '_ \
    //   | | (_) | (_) | | |_| | |_) |
    //   |_|\___/ \___/|_|\__|_| .__/
    //                         |_|
    ////////////////////////////////////////
    openBoardTooltip(col, row, x) {
      let uid = col + '_' + row;
      if (this._boardTooltips[uid]) {
        // TODO
      } else {
        let cell = this._grid[col][row];

        let w = 310;
        let container = $('m44-central-part');
        if (x > w) {
          cell.openingPosition = 'left';
        } else if (x < container.offsetWidth - w) {
          cell.openingPosition = 'right';
        } else {
          return; // Not enough place to show them !
        }

        if (
          (cell.terrains.length == 0 || this._terrainsVisibility == 0) &&
          !this.cellHasUnitTooltip(cell) &&
          (cell.tokens.length == 0 || this._tokensVisibility == 0)
        ) {
          return; // Nothing to show !
        }

        let tooltip = this.place('tplBoardTooltip', cell, container);
        tooltip.innerWidth;
        tooltip.classList.add('open');
        this._boardTooltips[uid] = tooltip;
      }
    },

    closeBoardTooltip(col, row) {
      let uid = col + '_' + row;
      if (!this._boardTooltips[uid]) return;

      this._boardTooltips[uid].remove();
      delete this._boardTooltips[uid];
    },

    closeAllTooltips() {
      Object.keys(this._boardTooltips).forEach((uid) => {
        this._boardTooltips[uid].remove();
      });
      this._boardTooltips = {};
    },

    openBoardTooltipModal(col, row) {
      let cell = this._grid[col][row];
      if (
        (cell.terrains.length == 0 || this._terrainsVisibility == 0) &&
        !this.cellHasUnitTooltip(cell) &&
        (cell.tokens.length == 0 || this._tokensVisibility == 0)
      ) {
        return false; // Nothing to show !
      }

      let modal = new customgame.modal('showHexInfo', {
        class: 'memoir44_popin',
        closeIcon: 'fa-times',
        contents: '<div id="hex-info-modal"></div>',
        breakpoint: 800,
        scale: 0.8,
        autoShow: true,
      });

      this.place('tplBoardTooltip', cell, 'hex-info-modal');
      return true;
    },

    openAllTooltipModal() {
      let modal = new customgame.modal('showAllSummary', {
        class: 'memoir44_popin',
        closeIcon: 'fa-times',
        contents: '<div id="all-summary-modal" class="board-tooltip"></div>',
        breakpoint: 800,
        scale: 0.8,
        verticalAlign: 'flexStart',
        autoShow: true,
      });

      let tooltips = [];
      // Special rules
      if (this.gamedatas.scenario && this.gamedatas.scenario.game_info && this.gamedatas.scenario.game_info.options) {
        let options = this.gamedatas.scenario.game_info.options;
        Object.keys(options).forEach((option) => {
          tooltips.push({
            tpl: 'tplSpecialRuleSummary',
            t: { name: option, val: options[option] },
            n: 0,
          });
        });
      }

      let terrainNumbers = [];
      let unitNumbers = [];
      Object.keys(this._grid).forEach((col) => {
        Object.keys(this._grid[col]).forEach((row) => {
          let cell = this._grid[col][row];

          cell.terrains.forEach((terrain) => {
            if (!terrainNumbers.includes(terrain.number)) {
              tooltips.push({
                tpl: 'tplTerrainSummary',
                t: terrain,
                n: terrain.number,
              });
              terrainNumbers.push(terrain.number);
            }
          });
          cell.tokens.forEach((token) => {
            if (!this.includesToken(tooltips, token)) {
              tooltips.push({
                tpl: 'tplTokenSummary',
                t: token,
                n: token.number,
              });
            }
          });
          if (cell.unit && !unitNumbers.includes(cell.unit.number)) {
            tooltips.push({
              tpl: 'tplUnitSummary',
              t: cell.unit,
              n: cell.unit.number,
            });
            unitNumbers.push(cell.unit.number);
          }
        });
      });

      // Sort
      const TYPE_ORDER = {
        tplUnitSummary: 1,
        tplTerrainSummary: 2,
        tplTokenSummary: 3,
      };
      tooltips.sort(function (x, y) {
        let d = TYPE_ORDER[x.tpl] - TYPE_ORDER[y.tpl];
        return d == 0 ? (x.n < y.n ? -1 : 1) : d;
      });

      tooltips.forEach((data) => {
        this.place(data.tpl, data.t, 'all-summary-modal');
      });
    },

    // Check whether we already have a similar token or not
    includesToken(tooltips, token) {
      let attributes = [];
      if (token.type == TOKEN_MEDAL) {
        attributes = ['counts_for', 'last_to_occupy', 'majority', 'nbr_hex', 'permanent', 'sole_control', 'turn_start'];
      }

      for (let i = 0; i < tooltips.length; i++) {
        let obj = tooltips[i];
        if (obj.tpl != 'tplTokenSummary') continue;
        if (obj.t.type != token.type) continue;
        if (obj.t.team != token.team) continue;

        if (attributes.reduce((c, attr) => c && obj.t.datas[attr] == token.datas[attr], true)) {
          return true;
        }
      }

      return false;
    },

    cellHasUnitTooltip(cell) {
      if (!cell.unit) return false;
      let myNations = this._bottomTeam == 'AXIS' ? ['ger', 'jp'] : ['us', 'ru', 'brit'];
      let mySide = myNations.includes(cell.unit.nation);
      let pref = mySide ? this._ownUnitsVisibility : this._opponentUnitsVisibility;
      return pref == 1;
    },

    tplBoardTooltip(cell) {
      let terrainDivs =
        this._terrainsVisibility == 0 ? [] : cell.terrains.map((terrain) => this.tplTerrainSummary(terrain));
      let tokenDivs = this._tokensVisibility == 0 ? [] : cell.tokens.map((token) => this.tplTokenSummary(token));
      let unitDiv = this.cellHasUnitTooltip(cell) ? this.tplUnitSummary(cell.unit) : '';

      return `<div class='board-tooltip' style='${cell.openingPosition}:0px'>${unitDiv} ${terrainDivs.join(
        '',
      )} ${tokenDivs.join('')}</div>`;
    },

    tplSpecialRuleSummary(rule) {
      let name = '';
      let tile = '';
      let desc = [];

      if (rule.name == 'russian_commissar_rule') {
        name = _('Red Army (RKKA)');
        tile = '<div class="commissar-token"></div>';
        desc = [
          '<li>' +
            _(
              'A command card cannot be played directly from hand. Instead, it must be placed under the commissar chip in preparation for a future turn.',
            ) +
            '</li>',
          '<li>' +
            _('Recon 1, Counter-attack and Ambush cards are exceptions; they may be played as normal.') +
            '</li>',
          '<li>' +
            _(
              "Otherwise, the Command card already under the commissar chip is the player's command card for the turn.",
            ) +
            '</li>',
        ];
      } else if (rule.name == 'blitz_rules') {
        name = _('Blitz rules');
        tile = '';
        desc = [
          '<li>' + _('Axis may play a Recon 1 card as an Air Power card in same section') + '</li>',
          '<li>' + _('Allied Armor move 2 hexes max and Axis Armor move 3 hexes') + '</li>',
        ];
      } else if (rule.name == 'british_commonwealth') {
        name = _('Commonwealth');
        tile = '';
        desc = [
          '<li>' +
            _(
              "A BCF ground unit that survives an ennemy's Close Assault combat without retreating and is down to a single figure may immediately battle that ennemy back with 1d",
            ) +
            '</li>',
          '<li>' + _('A battle back ignores all terrain battle dice restrictions') + '</li>',
          '<li>' + _('A battle back may occur even if the Close Assault is part of an Armor Overrun') + '</li>',
          '<li>' + _('The unit cannot battle back during an Ambush') + '</li>',
        ];
      } else if (rule.name == 'italy_royal_army') {
        name = _('Italian Royal Army');
        tile = '';
        desc = [
          '<li>' + _('Start with 6 commands cards; discard one for each unit lost, but never go below 3') + '</li>',
          '<li>' + _('All Italian ground units may retreat 1 to 3 hexes per flag') + '</li>',
          '<li>' + _('All Italian Artillery units may ignore 1 flag') + '</li>',
        ];
      } else if (rule.name == 'north_african_desert_rules') {
        name = _('Desert Rules');
        tile = '';
        desc = [
          '<li>' +
            _(
              'On successfull Close Assault, Armor may move into vacated hex and move 1 additional hex before battling again.',
            ) +
            '</li>',
        ];
      } else if (rule.name == 'partial_blitz_rules') {
        name = _('Partial Blitz rules');
        tile = '';
        desc = ['<li>' + _('Allied Armor move 2 hexes max and Axis Armor move 3 hexes') + '</li>'];
      }

      return `<div class='summary-card summary-rules'>
        <div class='summary-name'>${name}</div>
        <div class='summary-tile'>
          ${tile}
        </div>
        <ul class='summary-desc'>
          ${desc.join('')}
        </ul>
      </div>`;
    },

    tplTerrainSummary(terrain) {
      if (!terrain.properties) terrain.properties = {};
      let terrainData = Object.assign({}, this.gamedatas.terrains[terrain.number], terrain, terrain.properties);
      let tplName = OBSTACLES.includes(terrainData.tile) ? 'tplObstacleTile' : 'tplTerrainTile';
      let tile = this[tplName](terrainData);
      let desc = terrainData.desc.map((t) => `<li>${_(t)}</li>`);

      let properties = {
        isImpassable: {
          bool: _('Impassable'),
          obj: _('Impassable by ${units}'),
        },
        mustBeAdjacentToEnter: {
          bool: _('To enter or take ground, unit must start its move from adjacent hex'),
          obj: _('To enter or take ground, ${units} must start its move from adjacent hex'),
        },
        mustStopMovingWhenEntering: {
          bool: _('Unit moving in must stop'),
          obj: _('${units} moving in must stop'),
        },
        mustStopWhenEntering: {
          bool: _('Unit moving in must stop and may move no further on that turn'),
          obj: _('${units} moving in must stop and may move no further on that turn'),
        },
        mustStopWhenLeaving: {
          bool: _('When exiting, unit must stop on adjacent hex, may still take ground'),
          obj: _('When exiting, ${units} must stop on adjacent hex, may still take ground'),
        },
        cantLeave: {
          bool: _('Unit may not retreat, must take loss instead'),
          obj: _('${units} may not retreat, must take loss instead'),
        },
        isImpassableForRetreat: {
          bool: _('Unit cannot retreat on that hex'),
          obj: _('${units} cannot retreat on that hex'),
        },
        enteringCannotBattle: {
          bool: _('Unit moving in cannot battle'),
          obj: _('${units} moving in cannot battle'),
        },
        cannotBattle: {
          bool: _('Unit cannot battle on that hex'),
          obj: _('${units} cannot battle on that hex'),
        },
        canIgnoreOneFlag: {
          bool: _('Unit may ignore 1 flag'),
          obj: _('${units} may ignore 1 flag'),
        },
        canIgnoreAllFlags: {
          bool: _('Unit may ignore all flags'),
          obj: _('${units} may ignore all flags'),
        },
        isBlockingLineOfSight: {
          bool: _('Block line of sight'),
          negbool: _('Do not block line of sight'),
          obj: _('Block line of sight of ${units}'),
          hill: _('Block line of sight (except for contiguous adjacent hills)'),
        },
        hill317: {
          bool: _('Hill317: If Allies has a unit on the hill, Recon cards can be played as Air Power card'),
          obj: '',
        },
        isBlockingLineOfAttack: {
          bool: _('Block line of attack for all units (including Artillery)'),
        },
        canRecover: {
          bool: _(
            'An ordered Infantry unit, with no adjacent enemy units, may recover lost figures. Resolution is done like for Medics card. The unit cannot move or battle this turn',
          ),
        },
      };

      let unitMap = {
        1: _('Infantry'),
        2: _('Armor'),
        3: _('Artillery'),
      };
      let alliedUnitMap = {
        1: _('Allied Infantry'),
        2: _('Allied Armor'),
        3: _('Allied Artillery'),
      };
      let axisUnitMap = {
        1: _('Axis Infantry'),
        2: _('Axis Armor'),
        3: _('Axis Artillery'),
      };

      let movementProperties = [
        'isImpassable',
        'mustBeAdjacentToEnter',
        'mustStopMovingWhenEntering',
        'mustStopWhenEntering',
        'mustStopWhenLeaving',
        'cantLeave',
        'cantRetreat',
      ];
      let movementRestriction = false;

      let combatProperties = ['enteringCannotBattle', 'cannotBattle'];
      let combatRestriction = false;

      Object.keys(properties).forEach((prop) => {
        let content = '';
        let propDesc = properties[prop];
        if (terrainData[prop]) {
          if (movementProperties.includes(prop)) {
            movementRestriction = true;
          }
          if (combatProperties.includes(prop)) {
            combatRestriction = true;
          }

          if (isObject(terrainData[prop])) {
            let units = '';
            if (terrainData[prop].ALLIES) {
              units += Array.isArray(terrainData[prop].ALLIES)
                ? terrainData[prop].ALLIES.map((unitId) => alliedUnitMap[unitId]).join(' & ')
                : _('Allied Units');
            }
            if (terrainData[prop].AXIS) {
              if (units != '') {
                units += ' & ';
              }
              units += Array.isArray(terrainData[prop].AXIS)
                ? terrainData[prop].AXIS.map((unitId) => axisUnitMap[unitId]).join(' & ')
                : _('Axis Units');
            }
            content = dojo.string.substitute(propDesc.obj, { units });
          } else if (Array.isArray(terrainData[prop])) {
            let units = terrainData[prop].map((unitId) => unitMap[unitId]).join(' & ');
            content = dojo.string.substitute(propDesc.obj, { units });
          } else {
            content = terrainData[prop] == -1 ? '' : propDesc.bool;

            if (prop == 'isBlockingLineOfSight' && terrainData['isBlockingLineOfAttack']) {
              content = '';
            }
          }
        } else if (propDesc.negbool) {
          content = propDesc.negbool;

          if (prop == 'isBlockingLineOfSight' && terrainData.isHill) {
            content = propDesc.hill;
          }
        }

        if (prop == 'canIgnoreOneFlag' && terrainData['canIgnoreAllFlags']) {
          return;
        }

        if (content != '') {
          let modified = terrain.properties && terrain.properties[prop] !== undefined;
          desc.push(`<li class='${modified ? 'modified' : ''}'>${content}</li>`);
        }

        if (prop == 'cantRetreat' && !movementRestriction) {
          desc.push(`<li>${_('No movement restrictions')}</li>`);
        }
        if (prop == 'cannotBattle' && !combatRestriction && !terrainData['defense'] && !terrainData['offense']) {
          desc.push(`<li>${_('No combat restrictions')}</li>`);
        }
      });

      if (terrainData['defense']) {
        let defense = [];
        Object.keys(unitMap).forEach((type) => {
          if (terrainData['defense'][type]) {
            defense.push(
              this.strReplace(_('${unit} battles in ${nb}'), { unit: unitMap[type], nb: terrainData['defense'][type] }),
            );
          }
        });
        if (defense.length > 0) {
          let modified = terrain.properties && terrain.properties['defense'] !== undefined;
          desc.push(`<li class='${modified ? 'modified' : ''}'>${defense.join(', ')}</li>`);
        }
      }

      if (terrainData['offense']) {
        let offense = [];
        Object.keys(unitMap).forEach((type) => {
          if (terrainData['offense'][type]) {
            offense.push(
              this.strReplace(_('${unit} battles out at ${nb}'), {
                unit: unitMap[type],
                nb: terrainData['offense'][type],
              }),
            );
          }
        });
        if (offense.length > 0) {
          let modified = terrain.properties && terrain.properties['offense'] !== undefined;
          desc.push(`<li class='${modified ? 'modified' : ''}'>${offense.join(', ')}</li>`);
        }
      }

      // Remove letter in number (used for bis/ter for some terrains)
      let number = String(terrainData.number).replace(/\D/g, '');

      return `<div class='summary-card summary-terrain'>
        <div class='summary-number'>${number}</div>
        <div class='summary-name'>${_(terrainData.name)}</div>
        <div class='summary-tile'>
          ${tile}
        </div>
        <ul class='summary-desc'>
          ${desc.join('')}
        </ul>
      </div>`;
    },

    tplUnitSummary(unit) {
      if (!unit.properties) unit.properties = {};
      let unitData = Object.assign({}, this.gamedatas.units[unit.number], unit, unit.properties);
      let tile = this.tplUnit(unitData, true);
      let desc = unitData.desc.map((t) => `<li>${_(t)}</li>`);
      let isModified = (prop) => unit.properties[prop] !== undefined;

      let content = this.strReplace(
        unitData.movementRadius == unitData.movementAndAttackRadius
          ? _('Move 0-${maxBattle} and battle')
          : unitData.movementAndAttackRadius == 0
          ? unitData.movementRadius == 1
            ? _('Move 1 or battle')
            : _('Move 1-${maxMove} or battle')
          : _('Move 0-${maxBattle} and battle or move ${maxMove} no battle'),
        { maxBattle: unitData.movementAndAttackRadius, maxMove: unitData.movementRadius },
      );
      desc.push(
        `<li class='${
          isModified('movementRadius') || isModified('movementAndAttackRadius') ? 'modified' : ''
        }'>${content}</li>`,
      );

      let divPowers = ['<div class="fire-power"><span>0</span></div>'];
      unitData.attackPower.forEach((power) => divPowers.push(`<div class="fire-power"><span>${power}</span></div>`));
      let power = divPowers.join('');
      desc.push(
        `<li class='${
          isModified('attackPower') ? 'modified' : ''
        }'><div class="fire-power-handler">${power}</div></li>`,
      );

      if (unitData.ignoreDefense) {
        desc.push(
          `<li class='${isModified('ignoreDefense') ? 'modified' : ''}'>${_(
            'Ignore terrain battle restrictions',
          )}</li>`,
        );
      }

      if (unitData.equipment != false) {
        if (unitData.equipment == 'boat') {
          desc.push(`<li>${_('has boat. Can cross the river once')}</li>`);
        }
      }

      // Remove letter in number (used for bis/ter for some terrains)
      let number = String(unitData.number).replace(/\D/g, '');

      return `<div class='summary-card summary-unit'>
        <div class='summary-number'>${number}</div>
        <div class='summary-name'>${_(unitData.name)}</div>
        <div class='summary-tile'>
          ${tile}
        </div>
        <ul class='summary-desc'>
          ${desc.join('')}
        </ul>
      </div>`;
    },

    tplTokenSummary(token) {
      let name = '';
      let desc = [];
      let tile = '';

      // Medals
      if (token.type == TOKEN_MEDAL) {
        let sides = {
          null: _('Anyone units'),
          ALLIES: _('Allied units'),
          AXIS: _('Axis units'),
        };
        let hexes = token.datas.group.map((cell) => computeCoords(cell.x, cell.y)).join(', ');
        let subst = {
          units: sides[token.team],
          nb: token.datas.nbr_hex,
          counts_for: token.datas.counts_for,
          hexes,
        };
        tile = this.tplBoardMedal(token);

        if (token.datas.counts_for == 100) {
          name = _('Sudden death');
          let msg =
            token.datas.group.length == 1
              ? _('If ${units} occupy this hex at the end of their turn, they win immediately')
              : _('If ${units} occupy ${nb} hexes in the group ${hexes}, they win immediately');
          desc = ['<li>' + this.strReplace(msg, subst) + '</li>'];
        } else {
          name = _('Objective medal');
          let msg =
            token.datas.group.length == 1
              ? _('If ${units} occupy this hex, they win ${counts_for} victory medal(s)')
              : _('If ${units} occupy ${nb} hexes in the group ${hexes}, they win ${counts_for} victory medals(s)');

          desc = [
            '<li>' + this.strReplace(msg, subst) + '</li>',
            '<li>' +
              (token.datas.permanent
                ? _(
                    'The medal(s), once gained, continues to count toward the victory, even if the conditions is no longer satisfied',
                  )
                : _('Remove the medal(s) if this is no longer the case')) +
              '</li>',
          ];

          if (token.datas.majority) {
            desc.push('<li>' + _('Majority medal') + '</li>');
          }
        }
      } else {
        tile = this.tplBoardToken(token);
        if (token.type == TOKEN_CAMOUFLAGE) {
          name = _('Camouflage');
          desc = [
            '<li>' + _('A camouflaged unit may only be targeted in a Close Assault') + '</li>',
            '<li>' + _('A Camouflaged unit that moves or battles lose its Camouflage') + '</li>',
          ];
        } else if (token.type == TOKEN_EXIT_MARKER) {
          name = _('Exit Markers');
          desc = [
            '<li>' +
              _('Markers designate specific hex(es) through which a unit exiting the board earn a medal') +
              '</li>',
          ];
        }
      }

      return `<div class='summary-card summary-token'>
        <div class='summary-name'>${name}</div>
        <div class='summary-tile'>
          ${tile}
        </div>
        <ul class='summary-desc'>
          ${desc.join('')}
        </ul>
      </div>`;
    },

    //////////////////////////////////////////
    //    _   _       _ _
    //   | | | |_ __ (_) |_ ___
    //   | | | | '_ \| | __/ __|
    //   | |_| | | | | | |_\__ \
    //    \___/|_| |_|_|\__|___/
    //
    //////////////////////////////////////////

    tplUnit(unit, tooltip = false) {
      let classNames = [];
      if (unit.activationCard > 0 && !tooltip) classNames.push('activated');
      if (unit.onTheMove && !tooltip) classNames.push('onTheMove');

      const RECT_UNITS = [5, 6, 7];
      unit.orientation = this._bottomTeam != (ALLIES_NATIONS.includes(unit.nation) ? 'ALLIES' : 'AXIS') ? 1 : 0;
      let rotation = 0;
      if (RECT_UNITS.includes(parseInt(unit.type))) {
        rotation = unit.rotate ? 6 : 0;
        if (unit.orientation != 1) {
          let angle = UNITS_ROTATION[unit.type] / -30;
          rotation += (unit.orientation - 1) * angle + 12;
        }
        rotation = rotation % 12;
      }

      tpl = `
      <div id="unit-${unit.id}${tooltip ? '-tooltip' : ''}" class="m44-unit ${classNames.join(' ')}"
        data-figures="${tooltip ? 1 : unit.figures}"
        data-type="${unit.type}" data-nation="${unit.nation}" data-orientation="${unit.orientation}"
        data-badge="${unit.badge}" data-rotation="${rotation}">
        <div class='m44-meeples-container'>
          <div class="m44-unit-meeple"></div>
          <div class="m44-unit-meeple"></div>
          <div class="m44-unit-meeple"></div>
          <div class="m44-unit-meeple"></div>
          <div class="m44-unit-meeple"></div>
        </div>`;
      if (unit.hasOwnProperty('equipment') && unit.equipment != false) {
        tpl = tpl + `<div id="board-token-unit-${unit.id}" class="board-token" data-sprite="-1"></div>`;
      }
      return tpl + `</div>`;
    },

    makeUnitsSelectable(units, callback, checkCallback, className = 'selected', updateButtonCallback = null) {
      this._selectedUnits = [];
      this._selectableUnits = units;
      this._checkCallbackForSelectingUnit = checkCallback;

      Object.keys(units).forEach((unitId) => {
        this.onClick('unit-' + unitId, () => {
          let unitIndex = this._selectedUnits.findIndex((t) => t == unitId);
          let selected = unitIndex !== -1; // Already selected ?
          // Should we take the click into account ?
          if (callback(unitId, this._selectableUnits[unitId], selected)) {
            if (selected) {
              this._selectedUnits.splice(unitIndex, 1);
              // Ad-hoc case when a selected unit switch to "on the move" instead of completely unselected
              if (className != 'activated' || !$('unit-' + unitId).classList.contains('onTheMove')) {
                $('unit-' + unitId).classList.remove(className);
              }
            } else {
              this._selectedUnits.push(unitId);
              $('unit-' + unitId).classList.add(className);
            }
          }

          if (updateButtonCallback) {
            updateButtonCallback();
          }

          // Update unselectable units
          let minFilling = this.getMinFillingOfSections();
          Object.keys(this._selectableUnits).forEach((unitId) => {
            let unitIndex = this._selectedUnits.findIndex((t) => t == unitId);
            let selected = unitIndex !== -1; // Already selected ?
            let selectable = checkCallback(unitId, this._selectableUnits[unitId], selected, minFilling);
            $('unit-' + unitId).classList.toggle('unselectable', !selectable);
          });
        });
      });

      this.clearSelectedUnits = () => {
        this._selectedUnits.forEach((unitId) => {
          $('unit-' + unitId).classList.remove(className);
        });
        this._selectedUnits = [];

        Object.keys(this._selectableUnits).forEach((unitId) => {
          let selectable = this._checkCallbackForSelectingUnit(unitId, this._selectableUnits[unitId], false, []);
          $('unit-' + unitId).classList.toggle('unselectable', !selectable);
        });
      };
    },

    /**
     * getMinFillingOfSections: handle units that are on two sections
     */
    getMinFillingOfSections() {
      let fillings = [[0, 0, 0]];
      this._selectedUnits.forEach((unitId) => {
        if (!this._selectableUnits[unitId].sections) return;
        let t = [];
        this._selectableUnits[unitId].sections.forEach((section) => {
          fillings.forEach((filling) => {
            let newFilling = filling.slice();
            newFilling[section]++;
            t.push(newFilling);
          });
        });
        fillings = t;
      });
      // Keep only compatible fillings
      let sections = this.getArgs().sections;
      let marineCommand = this.getArgs().marineCommand || false;
      if (sections) {
        fillings = fillings.filter(
          (filling) =>
            (filling[0] <= sections[0] && filling[1] <= sections[1] && filling[2] <= sections[2]) ||
            (marineCommand &&
              filling[0] <= sections[0] + 1 &&
              filling[1] <= sections[1] &&
              filling[2] <= sections[2]) ||
            (marineCommand &&
              filling[0] <= sections[0] &&
              filling[1] <= sections[1] + 1 &&
              filling[2] <= sections[2]) ||
            (marineCommand && filling[0] <= sections[0] && filling[1] <= sections[1] && filling[2] <= sections[2] + 1),
        );
      }

      let minFilling = [10, 10, 10];
      fillings.forEach((filling) => {
        for (let i = 0; i < 3; i++) {
          minFilling[i] = Math.min(minFilling[i], filling[i]);
        }
      });
      return minFilling;
    },

    notif_activateUnits(n) {
      debug('Notif: activating units', n);
      n.args.unitIds.forEach((unitId) => {
        $(`unit-${unitId}`).classList.add('activated');
        $(`unit-${unitId}`).removeAttribute('data-selected');
      });
      if (n.args.unitOnTheMoveIds) {
        n.args.unitOnTheMoveIds.forEach((unitId) => $('unit-' + unitId).classList.add('activated', 'onTheMove'));
      }
    },

    notif_clearUnitsStatus(n) {
      debug('Notif: clearing units status');
      this.removeClassNameOfCells('activated');
      this.removeClassNameOfCells('onTheMove');
    },

    notif_airDrop(n) {
      debug('Notif: air dropping a unit', n);
      let unit = n.args.unit;
      unit.orientation = this._bottomTeam != (ALLIES_NATIONS.includes(unit.nation) ? 'ALLIES' : 'AXIS') ? 1 : 0;
      this.place('tplUnit', unit, `cell-${unit.x}-${unit.y}`);
    },

    ////////////////////////////
    //    ____  _
    //   |  _ \(_) ___ ___
    //   | | | | |/ __/ _ \
    //   | |_| | | (_|  __/
    //   |____/|_|\___\___|
    /////////////////////////////
    rollDice(results, cell) {
      if (this.isFastMode()) return;

      let o = $('diceContainer');
      o.style.gridArea = $(`cell-container-${cell.x}-${cell.y}`).style.gridArea;
      o.style.display = 'grid';
      dojo.empty(o);

      results.forEach((result, i) => {
        let die = this.place('tplDice', { result, status: 'preAnimation' }, o);
        this.wait(i * 100).then(() => {
          die.querySelector('.m44-dice-wrapper').classList.add('animated');
          dojo
            .fadeOut({
              node: die,
              duration: 800,
              delay: 2000,
            })
            .play();
        });
      });

      this.wait(results.length * 100 + 3000).then(() => dojo.empty(o));
    },

    tplDice(dice) {
      return `<div class='m44-dice-resizable'>
        <div class="m44-dice-wrapper ${dice.status}" data-result="${dice.result}">
          <div class="m44-dice-shadow"></div>
          <div class="m44-dice"></div>
        </div>
      </div>`;
    },

    notif_rollDice(n) {
      debug('Notif: rolling dice', n);
      let cell = n.args.cell ? n.args.cell : { x: 0, y: 0 };
      this.rollDice(n.args.results, cell);
    },

    ///////////////////////////////////////////////////
    //    ____
    //   |  _ \  __ _ _ __ ___   __ _  __ _  ___  ___
    //   | | | |/ _` | '_ ` _ \ / _` |/ _` |/ _ \/ __|
    //   | |_| | (_| | | | | | | (_| | (_| |  __/\__ \
    //   |____/ \__,_|_| |_| |_|\__,_|\__, |\___||___/
    //                                |___/
    ///////////////////////////////////////////////////
    notif_takeDamage(n) {
      debug('Notif: a unit is taking damage', n);
      let unit = $('unit-' + n.args.unitId);
      unit.classList.remove('airPowerTarget');
      let callback = () => {
        unit.dataset.figures -= n.args.hits;
        if (unit.dataset.figures <= 0) {
          unit.remove();
          this._grid[n.args.cell.x][n.args.cell.y].unit = null;
        }
      };

      if (this.isFastMode()) {
        callback();
        return;
      }

      let o = $('explosionContainer');
      o.style.gridArea = $(`cell-container-${n.args.cell.x}-${n.args.cell.y}`).style.gridArea;
      o.style.display = 'flex';
      dojo.place('<div class="m44-explosion"></div>', o);
      this.wait(1000).then(() => {
        dojo.empty(o);
        callback();
      });
    },

    notif_miss(n) {
      debug('Notif: a unit was missed', n);
      let unit = $('unit-' + n.args.unitId);
      unit.classList.remove('airPowerTarget');
    },

    notif_exitUnit(n) {
      debug('Notif: exit unit', n);
      let unit = $('unit-' + n.args.unitId);
      unit.dataset.figures;
      unit.remove();
      this._grid[n.args.cell.x][n.args.cell.y].unit = null;
    },
  });
});
