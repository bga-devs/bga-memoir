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
  const OBSTACLES_ROTATION = { bunker: 180,wbunker : 180,dbunker : 180,ford : 60,roadblock : 60,droadblock : 60,wroadblock : 60,pontoon : -30,wpontoon : -30,dragonteeth : 60,railbridge : -60,wrailbridge : -60,bridge : -30,pbridge : -30,brkbridge : -30,wbridge : -30,wagon : -60,loco : 60,abatis : 60,wire : 180,sand : 180};

  const ALLIES_NATIONS = ['gb', 'us', 'ru'];

  const TOKEN_MEDAL = 1;
  const TOKEN_MINE = 2;
  const TOKEN_CAMOUFLAGE = 4;
  const TOKEN_EXIT_MARKER = 5;
  const TOKEN_ON_TOP = ['target'];

  function computeCoords(x, y) {
    // TODO : replace 9 by dim.y
    return String.fromCharCode(65 + (x % 2 == 0 ? 0 : 32) + parseInt(x / 2)) + (9 - y);
  }

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
          cell.style.gridArea = cellC.style.gridArea;

          let unit = board.grid[col][row].unit;
          if (unit) {
            unit.orientation = this._bottomTeam != (ALLIES_NATIONS.includes(unit.nation) ? 'ALLIES' : 'AXIS') ? 1 : 0;
            this._grid[col][row].unit = unit;
            this.place('tplUnit', unit, `cell-${col}-${y}`);
          }

          // Tokens/medals
          board.grid[col][row].tokens.forEach((token) => this.addToken(token));

          // Add tooltip listeners
          cell.addEventListener('mouseenter', () => {
            if (this._summaryCardsBehavior == 1) {
              this.openBoardTooltip(col, row);
            }
          });
          cell.addEventListener('mouseleave', () => {
            if (this._summaryCardsBehavior == 1) {
              this.closeBoardTooltip(col, row);
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

      this._boardScale = 1; // TODO localStorage
      dojo.connect($('m44-board-zoom-in'), 'click', () => this.incBoardScale(0.1));
      dojo.connect($('m44-board-zoom-out'), 'click', () => this.incBoardScale(-0.1));

      ['terrains', 'units', 'tokens', 'labels', 'coords'].forEach((layer) => {
        this.toggleLayerVisibility(layer, this.getConfig('m44' + layer, 1));
        dojo.connect($(`m44-${layer}-settings`), 'click', () => this.toggleLayerVisibility(layer));
      });

      this._summaryCardsBehavior = this.getConfig('m44summaryCards', this.isMobile() ? 2 : 1);
      $('m44-board-wrapper').dataset.summary = this._summaryCardsBehavior;
      dojo.connect($('m44-summary-settings'), 'click', () => this.changeSummaryCardsBehavior());
    },

    removeClassNameOfCells(className) {
      let cells = [...$('m44-board').getElementsByClassName(className)];
      cells.forEach((cell) => cell.classList.remove(className));
    },

    toggleLayerVisibility(layer, value = null) {
      let name = '_' + layer + 'Visibility';
      if (value == null) {
        this[name] = 1 - this[name];
      } else {
        this[name] = value;
      }

      $('m44-board-wrapper').dataset[layer] = this[name];
      localStorage.setItem('m44' + layer, this[name]);
    },

    changeSummaryCardsBehavior() {
      let val = parseInt(this._summaryCardsBehavior + 1) % 3;
      this._summaryCardsBehavior = val;
      $('m44-board-wrapper').dataset.summary = val;
      localStorage.setItem('m44summaryCards', val);

      if (val == 1 && this.isMobile()) {
        this.changeSummaryCardsBehavior();
      }
      if (val == 2 && !this.isMobile()) {
        this.changeSummaryCardsBehavior();
      }
    },

    incBoardScale(delta) {
      this._boardScale += delta;
      document.documentElement.style.setProperty('--memoirBoardScale', this._boardScale);
      //TODO localStorage.setItem('agricolaCardScale', scale);
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

    notif_updateVisibility(n) {
      debug('Notif: visibility update', n);
      // TODO
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
    openBoardTooltip(col, row) {
      let uid = col + '_' + row;
      if (this._boardTooltips[uid]) {
        // TODO
      } else {
        let cell = this._grid[col][row];
        if (
          (cell.terrains.length == 0 || this._terrainsVisibility == 0) &&
          (cell.unit == null || this._unitsVisibility == 0) &&
          (cell.tokens.length == 0 || this._tokensVisibility == 0)
        ) {
          return; // Nothing to show !
        }

        let tooltip = this.place('tplBoardTooltip', cell, 'left-holder');
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

    tplBoardTooltip(cell) {
      let terrainDivs =
        this._terrainsVisibility == 0 ? [] : cell.terrains.map((terrain) => this.tplTerrainSummary(terrain));
      let tokenDivs = this._tokensVisibility == 0 ? [] : cell.tokens.map((token) => this.tplTokenSummary(token));
      let unitDiv = cell.unit && this._unitsVisibility == 1 ? this.tplUnitSummary(cell.unit) : '';

      return `<div class='board-tooltip'>${unitDiv} ${terrainDivs.join('')} ${tokenDivs.join('')}</div>`;
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
        cantRetreat: {
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

      let movementProperties = [
        'isImpassable',
        'mustBeAdjacentToEnter',
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

          if (Array.isArray(terrainData[prop])) {
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
              this.strReplace(_('${unit} battles at ${nb}'), { unit: unitMap[type], nb: terrainData['defense'][type] }),
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
          ? _('Move 0-${maxMove} or battle')
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

      return `
      <div id="unit-${unit.id}${tooltip ? '-tooltip' : ''}" class="m44-unit ${classNames.join(' ')}"
        data-figures="${tooltip ? 1 : unit.figures}"
        data-type="${unit.type}" data-nation="${unit.nation}" data-orientation="${unit.orientation}"
        data-badge="${unit.badge}">
        <div class='m44-meeples-container'>
          <div class="m44-unit-meeple"></div>
          <div class="m44-unit-meeple"></div>
          <div class="m44-unit-meeple"></div>
          <div class="m44-unit-meeple"></div>
          <div class="m44-unit-meeple"></div>
        </div>
      </div>`;
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
      n.args.unitIds.forEach((unitId) => $('unit-' + unitId).classList.add('activated'));
      if (n.args.unitOnTheMoveIds) {
        n.args.unitOnTheMoveIds.forEach((unitId) => $('unit-' + unitId).classList.add('activated onTheMove'));
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
