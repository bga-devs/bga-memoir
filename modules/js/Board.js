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
  const UNITS_ROTATION = { 7 : 60, 6 : 60};

  const ALLIES_NATIONS = ['brit', 'us', 'ru'];

  const TOKEN_MEDAL = 1;
  const TOKEN_MINE = 2;
  const TOKEN_CAMOUFLAGE = 4;
  const TOKEN_EXIT_MARKER = 5;
  const TOKEN_ON_TOP = ['target','tag14','tag15'];

  function computeCoords(x, y, dim_y) {
    return String.fromCharCode(65 + (x % 2 == 0 ? 0 : 32) + parseInt(x / 2)) + (dim_y - y);
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
      if (terrain.tile == 'hills' || terrain.tile == 'mountain' || terrain.tile == 'smoke0') {
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

    notif_flipSmokeScreen(n) {
      console.log('Notif: flip smoke screen markers to tile smoke1', n);
      let tile = 'smoke1';
      $(`terrain-${n.args.smokeId}`).dataset.tile = tile;
      this._grid[n.args.cell.x][n.args.cell.y].terrains.forEach((terrain) => {
        if (terrain.id == n.args.smokeId) {
          terrain.tile = tile;
        }
      });
      
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

      if (terrain.tile.substr(0, 5) == 'smoke') {
        // Special case of smoke screen
        tile = terrain.tile;
      }

      let rotation = terrain.rotate ? 6 : 0;
      //console.log('terrain.orientation' + terrain.id + terrain.orientation);
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
      return `<div id="terrain-${obstacle.id}" class="hex-grid-content hex-grid-obstacle selectable" data-tile="${tile}" data-rotation="${rotation}"></div>`;
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
      const SPRITES = ['tag1', 'tag4', 'tag5', 'mine0', 'mine1', 'mine2', 'mine3', 'mine4', 'mineX', 'tag14', 'tag15', 'target', 'smoke0', 'smoke1'];
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
        contents: `<div id="hex-info-modal"></div>`,
        title: _('Summary cards'),
        breakpoint: 800,
        scale: 0.8,
        autoShow: true,
      });

      this.place('tplBoardTooltip', cell, 'hex-info-modal');
      dojo.place(
        `<h4>${_(
          'Warning: your setting "show summary cards on click" is currently enabled. This implies you won\'t be able to click on your units, don\'t forget to disable it once you are done examinating the board by clicking on this icon next to the board.',
        )}
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
          <path style="stroke-width:0.31999999" d="M 48.141478,60.56 C 47.277856,59.196 45.236723,56.684899 43.605629,54.979776 40.837597,52.086115 40.458192,51.481578 40.630966,50.24 c 0.118794,-0.853654 1.045092,-1.6 1.98578,-1.6 0.625705,0 1.359238,0.40713 2.306188,1.28 0.763751,0.704 1.480535,1.28 1.592852,1.28 0.112316,0 0.204214,-3.321466 0.204214,-7.381037 0,-4.475987 0.130378,-7.624649 0.331258,-8 C 47.278858,35.393693 47.722326,35.2 48.468403,35.2 50.335552,35.2 50.56,35.718563 50.56,40.032438 v 3.82672 l 0.976518,-0.721968 c 0.906567,-0.670256 1.038368,-0.687366 1.84,-0.238886 0.474916,0.265696 0.909498,0.802272 0.965738,1.19239 0.125382,0.869722 0.211466,0.875584 1.017744,0.06931 1.28863,-1.28863 3.2,-0.42793 3.2,1.440976 v 0.910586 l 0.740624,-0.695783 C 60.224486,44.947856 61.247328,44.927328 62.08,45.76 c 0.580202,0.580202 0.64,1.066666 0.64,5.206432 0,4.772192 -0.424698,7.145165 -1.891008,10.56593 L 60.182742,63.04 h -5.23552 -5.23552 z M 5.5773238,55.558154 C 3.7493917,54.706982 2.0631192,52.871117 1.5742899,51.2 1.3620228,50.474342 1.280706,43.367254 1.3433356,31.014628 L 1.44,11.949256 2.4084594,10.383087 C 3.0084525,9.4127933 3.9728166,8.4484525 4.9431488,7.8484595 L 6.5093789,6.88 31.417722,6.792409 C 56.267434,6.705024 56.329818,6.706432 57.920358,7.3906646 58.797219,7.7678803 60.011728,8.6267158 60.619267,9.2991878 62.660406,11.558484 62.72,12.117884 62.72,29.01808 c 0,14.12039 -0.101376,15.952317 -0.818566,14.791882 -0.118986,-0.192525 -0.772538,-0.296999 -1.452343,-0.23217 -0.833021,0.07944 -1.290304,-0.02362 -1.402547,-0.316131 C 58.954944,43.022957 58.88,36.273197 58.88,28.262189 58.88,15.660859 58.81194,13.553293 58.375274,12.633089 57.355139,10.483318 58.330746,10.56 32,10.56 5.669255,10.56 6.6448605,10.483318 5.6247274,12.633089 5.1837184,13.562446 5.12,15.964288 5.12,31.658639 5.12,51.358678 5.1169859,51.325571 6.9988998,52.298746 7.8402237,52.733811 10.045776,52.8 23.701864,52.8 H 39.43551 l 1.162244,1.01969 c 0.639235,0.560832 1.424697,1.352832 1.745472,1.76 L 42.926451,56.32 25.063226,56.3168 7.2,56.3136 5.5773238,55.558006 Z M 27.940423,45.135926 C 26.677877,44.366099 26.24,43.579165 26.24,42.08 c 0,-1.496362 0.431855,-2.27593 1.593595,-2.876688 2.472896,-1.278784 5.104488,0.204512 5.119288,2.885485 0.0087,1.569136 -0.68479,2.631843 -2.104427,3.225005 -1.393363,0.582185 -1.691408,0.563955 -2.908033,-0.177876 z m 11.842905,-7.88822 c -0.29767,-0.775716 0.480403,-1.077399 2.792672,-1.082807 2.125818,-0.005 2.951302,0.355248 2.644758,1.154096 -0.263449,0.686535 -5.171318,0.622186 -5.43743,-0.07129 z m 12.164138,0.01078 c -0.334935,-0.872823 0.366092,-1.131252 2.803542,-1.033498 2.179373,0.0874 2.377178,0.152637 2.471248,0.815008 0.09882,0.69583 0.01523,0.72 -2.490042,0.72 -2.058486,0 -2.631929,-0.103274 -2.784748,-0.50151 z M 28.544439,34.42728 c 0.104111,-1.255437 0.610473,-2.671994 1.838293,-5.142656 1.334518,-2.685361 1.728679,-3.834441 1.854964,-5.407699 0.175123,-2.181666 -0.361991,-3.73841 -1.499971,-4.347439 -0.951375,-0.50916 -3.395286,-0.40669 -3.867385,0.162154 -0.342629,0.412842 -0.246333,0.649724 0.600817,1.47797 3.098997,3.029843 -1.573935,6.825737 -4.80412,3.902459 -1.40551,-1.271969 -0.978202,-4.221359 0.843298,-5.820657 2.14776,-1.885758 6.999796,-2.664634 10.47627,-1.68171 3.112275,0.879953 5.402237,3.989356 4.902265,6.656494 -0.447865,2.389183 -1.032323,3.195777 -4.108448,5.669949 -3.362915,2.704844 -3.86041,3.306578 -4.186645,5.063855 -0.200232,1.078557 -0.322474,1.2 -1.207903,1.2 h -0.985125 z m 15.203532,-1.078272 c -1.488947,-1.511678 -1.845888,-2.382393 -1.152349,-2.811023 0.53385,-0.329938 3.942791,3.199753 3.700384,3.831455 -0.324233,0.844938 -0.96247,0.589338 -2.548035,-1.020432 z M 50.88,34.109654 c 0,-0.942793 2.987744,-3.771018 3.696429,-3.499071 0.827379,0.317496 0.495885,1.052976 -1.206394,2.676607 C 51.519536,35.052192 50.88,35.26343 50.88,34.109654 Z m -3.005763,-1.015827 c -0.294077,-0.766349 -0.223069,-4.072816 0.09837,-4.580456 0.155542,-0.245647 0.513337,-0.358166 0.795101,-0.250044 0.410883,0.157672 0.512294,0.705335 0.512294,2.76663 0,2.308293 -0.0617,2.570043 -0.605763,2.570043 -0.333171,0 -0.693171,-0.227776 -0.8,-0.506173 z"
        />
      </h4>`,
        'hex-info-modal',
      );
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
        // No option in editor for Japanese Imperial Army but only Nation trigger
        if (this.gamedatas.scenario.game_info.country_player1 == 'JP' 
          || this.gamedatas.scenario.game_info.country_player2 == 'JP') {
          options.japanese_imperial = true;
        }
        // filter option with no rules (like mine_deck_name, ...) that may cause empty tooltip card
        const no_rules = ['mine_deck_name', 'empty_section_medals', 'deck_reshuffling', 'night_visibility_reverse_rule'];
        Object.keys(options).forEach((option) => {
          if (!no_rules.includes(option)) {
            tooltips.push({
              tpl: 'tplSpecialRuleSummary',
              t: { name: option, val: options[option] },
              n: 0,
            });
          }
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
      } else if (rule.name == 'air_strikes_rules') {
        name = _('Air Strikes rules');
        tile = '';
        desc = [
          '<li>' + _('When a player is capable of making Air Strikes, in conditions specified by the scenario, any Recon 1 card he plays may be played as an Air Power card instead (Air Sortie if Air Rules in effect)') + '</li>',
          '<li>' + _('This player may play a Recon 1 card as an Air Power card in same section') + '</li>',
        ];
      }else if (rule.name == 'british_commonwealth') {
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
      } else if (rule.name == 'gung_ho') {
        name = _('US Marines Corps');
        tile = '';
        desc = [
          '<li>' + _('Order 1 more unit than indicated on any Section card played') + '</li>',
          '<li>' + _('All Tactic cards that activate 1 to 4 units activate 2 to 5 instead') + '</li>',
          '<li>' + _('Marines counter-attack with +1 ordered unit against Japanese Command card. Opposite not true.') + '</li>',
          '<li>' + _('No effect on Air Power, Air Sortie, Artillery Bombard, Barrage, Close Assault, Infantry Assault, and Their Finest Hour.') + '</li>',     
        ];
      } else if (rule.name == 'japanese_imperial') {
        name = _('Imperial Japanese Army');
        tile = '';
        desc = [
          '<li>' + _('Infantry must always ignore 1 flag') + '</li>',
          '<li>' + _('When in terrain that ignores a flag, must ignore 2 flags instead') + '</li>',
          '<li>' + _('When in caves, must ignore all flags') + '</li>',
          '<li>' + _('Infantry at full strength in Close Assault battles at +1 die') + '</li>',
          '<li>' + _('Infantry may move 2 hexes to combat into Close Assault') + '</li>',     
        ];
      } else if (rule.name == 'night_visibility_rules') {
        name = _('Night Attacks');
        tile = '';
        desc = [
          '<li>' + _('Each turn Allied player rolls 4 dice, each Star increases visibility') + '</li>',
          '<li>' + _('When full daylight is reached chart set aside, normal visibility conditions resume') + '</li>',     
        ];
      } else if (rule.name == 'night_visibility_team_turn') {
        name = _('Night Fall Attacks (Axis)');
        tile = '';
        desc = [
          '<li>' + _('This specific rule supersedes standard Night Attack rules') + '</li>',
          '<li>' + _('Each turn Axis player rolls 4 dice, each Star decreases visibility') + '</li>',
          '<li>' + _('Visibility starts from 6 to 1') + '</li>',     
        ];
      } else if (rule.name == 'armor_breakthrough_rules') {
        name = _('Armor Breakthrough');
        tile = '';
        desc = [
          '<li>' + _('During a single game turn, you may order new Armor Units onto the board') + '</li>',
          '<li>' + _('Order must be valid and issued as normal') + '</li>',
          '<li>' + _('Units must enter through Opponent\'s baseline') + '</li>',
          '<li>' + _('Units must stop on baseline hex; they may battle (if no terrain restriction when entering) and Take Ground this turn, but may not Armor Overrun') + '</li>',
          '<li>' + _('When retreating, Units must retreat towrd controlling player\'s baseline') + '</li>',     
        ];
      } else if (rule.name == 'deck_name' && rule.val == 'AIR_POWER_AS_ARTILLERY_BOMBARD_DECK') {
        name = _('Special Deck');
        tile = '';
        desc = [
          '<li>' + _('Air Power card is played as Artillery Bombard card') + '</li>',
        ];
      } else if (rule.name == 'smoke_screen') {
        name = _('Smoke screen');
        tile = '<div class="smoke_screen"></div>';
        desc = [
          '<li>' + _('Place Smoke Screen markers on 3 adjacent contiguous hexes') + '</li>',
          '<li>' + _('Once your turn has elapsed, flip Smoke Screen markers over') + '</li>',
          '<li>' + _('Once your second turn has elapsed, remove Smoke Screen markers ') + '</li>',
          '<li>' + _('Units can move on and through Smoke Screen without penalty') + '</li>',
          '<li>' + _('Smoke Screen blocks line of Sight') + '</li>',
          '<li>' + _('Unit on Smoke Screen hex may be seen and see out of that hex') + '</li>',
        ];

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
      let heightdesc = _('Height : ');
      
      if (terrainData.height > 0) {
        desc.push(`<li class=''>${heightdesc}${terrainData.height}</li>`);
      }

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
          hill: _('Block line of sight (except for contiguous adjacent hills). Also, line of sight between two units that are on elevated terrain is never blocked by terrain or units that are on lower heights.'),
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
        let dim_y = this.gamedatas.board.type == 'BRKTHRU' ? 17 : 9;
        let hexes = token.datas.group.map((cell) => computeCoords(cell.x, cell.y, dim_y)).join(', ');
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

          if (token.datas.any_hex) {
            desc.push('<li>' + _('If any Objective hex is occupied by an Allied unit, the Allies get the Objective Medal and the Axis lose it.') + '</li>');
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
            '<li>' + _('Unit on the marker needs an additional hex move available to exit and gain the medal') + '</li>',
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
      unit.side = this._bottomTeam != (ALLIES_NATIONS.includes(unit.nation) ? 'ALLIES' : 'AXIS') ? 1 : 0;
      let rotation = 0;
      if (RECT_UNITS.includes(parseInt(unit.type))) {
        rotation = unit.rotate ? 6 : 0;
        console.log('rotation ' + rotation + ' unit.orientation ' + unit.orientation + ' unit.side ' + unit.side);
        if (unit.side != 1) {
          let angle = UNITS_ROTATION[unit.type] / 30;
          rotation += (unit.orientation - 1) * angle + 12;
          console.log('case unit.side not 1 : rotation ' + rotation + ' angle ' + angle);
        }
        rotation = rotation % 12;
        console.log('final rotation ' + rotation);
      } else {
        unit.orientation = this._bottomTeam != (ALLIES_NATIONS.includes(unit.nation) ? 'ALLIES' : 'AXIS') ? 1 : 0;
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
      // For Pincer Move and standard game, remove fillings in center section
      const pincer_move = [2, 0, 2];
      if(sections){
        if (sections.toString() == pincer_move.toString()) {
          fillings = fillings.filter(
            (filling) => (filling[1] == 0)
          );
        }
      }
      

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

    notif_trainReinforcement(n) {
      debug('Notif: reinforcing a unit from train', n);
      let unit = n.args.unit;
      unit.orientation = this._bottomTeam != (ALLIES_NATIONS.includes(unit.nation) ? 'ALLIES' : 'AXIS') ? 1 : 0;
      this.place('tplUnit', unit, `cell-${unit.x}-${unit.y}`);
    },

    notif_reserveUnitsDeployement(n) {
      debug('Notif: placing unit on the board from reserve', n);
      let unit = n.args.unit;
      let player_id =n.args.player_id;
      unit.orientation = this._bottomTeam != (ALLIES_NATIONS.includes(unit.nation) ? 'ALLIES' : 'AXIS') ? 1 : 0;
      if (n.args.stage_area) {
        //let stagingArea = [...$('bottom-staging-area').getElementsByClassName('reserve-container')];
        console.log('staging area notif');
        //this.place('tplUnit', unit, `reserve-1`);
        this.gamedatas.teams = n.args.teams;
        console.log(this.gamedatas.teams);
        this.updateTeams();   
      } else {
        this.place('tplUnit', unit, `cell-${unit.x}-${unit.y}`);
      }
      this._reserveTokenCounter[player_id].incValue(-1);
    },

    notif_armorBreakthroughDeployement(n) {
      debug('Notif: placing unit on the board Armor Breakthrough', n);
      let unit = n.args.unit;
      let player_id =n.args.player_id;
      unit.orientation = this._bottomTeam != (ALLIES_NATIONS.includes(unit.nation) ? 'ALLIES' : 'AXIS') ? 1 : 0;
      this.place('tplUnit', unit, `cell-${unit.x}-${unit.y}`);
    },

    notif_addAirPowerToken(n) {
      console.log('addAirPowerToken');
      team = n.args.team.team;
      this.addAirpowerToken(team);
    },

    notif_removeAirPowerToken(n) {
      console.log('removeAirPowerToken');
      team = n.args.team.team;
      this.removeAirpowerToken(team);
    },

    addAirpowerToken(team) {
      let pos = this._bottomTeam == team ? 'bottom' : 'top';
      let container = pos + '-reserve-2';
      dojo.place(`<div class="air_power_token"></div>`, container);
    },

    removeAirpowerToken(team) {
      let pos = this._bottomTeam == team ? 'bottom' : 'top';
      let container = pos + '-reserve-2';
      let tokenToRemove =  [...$(container).getElementsByClassName('air_power_token')]
      $(tokenToRemove[0]).remove();
      this.updateTeams();
    },

    notif_replenishWinnerReserveTokens(n) {
      console.log('notif replenish winner tokens', n.args, this._reserveTokenCounter[n.args.player_id] );
      //this._reserveTokenCounter[n.args.player_id] = incValue(n.args.nbAddedTokens);
      this.updatePlayers();
      // clean and remove all staging areas for all team
      let stagingAreaToRemove = [...$('bottom-staging-slots').getElementsByClassName('reserve-unit')];
      stagingAreaToRemove.push(...$('top-staging-slots').getElementsByClassName('reserve-unit'));
      stagingAreaToRemove.push(...$('bottom-staging-slots').getElementsByClassName('reserve-token'));
      stagingAreaToRemove.push(...$('top-staging-slots').getElementsByClassName('reserve-token'));
      console.log(stagingAreaToRemove);
    
      stagingAreaToRemove.forEach ((elem) => {
        $(elem).remove();
      });
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
      line_default = this.gamedatas.board.type == 'BRKTHRU' ? 8 : 4;
      let cell = n.args.cell ? n.args.cell : { x: 12, y: line_default }; // by default center of board cell-12-4 or 8 (standard or BT)
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
