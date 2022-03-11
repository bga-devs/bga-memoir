define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
  function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
  }

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

  return declare('memoir.board', null, {
    setupBoard() {
      let board = this.gamedatas.board;
      let pId = this.isSpectator ? Object.values(this.gamedatas.players)[0] : this.player_id;
      let bottomTeam = this.gamedatas.players[pId].team;
      let rotate = this.gamedatas.teams.find((team) => team.team == bottomTeam).position == 1;
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

          // Take into account rotation
          let realX = rotate ? 2 * size - col - (y % 2 == 0 ? 2 : 0) : col;
          let realY = rotate ? dim.y - y - 1 : y;

          // Background and terrains
          let tile = this.getBackgroundTile(face, dim, x, y);
          let cellC = this.place('tplBoardBackgroundCell', { x: col, y, tile, rotate }, 'm44-board-terrains');
          cellC.style.gridRow = 3 * realY + 3 + ' / span 4';
          cellC.style.gridColumn = realX + 2 + ' / span 2';
          board.grid[col][row].terrains.forEach((terrain) => {
            let tplName = TERRAINS.includes(terrain.tile) ? 'tplTerrainTile' : 'tplObstacleTile';
            terrain.rotate = rotate;
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
            unit.orientation = bottomTeam != (ALLIES_NATIONS.includes(unit.nation) ? 'ALLIES' : 'AXIS') ? 1 : 0;
            this.place('tplUnit', unit, `cell-${col}-${y}`);
          }

          // Medals
          board.grid[col][row].medals.forEach((medal) => this.place('tplBoardMedal', medal, cellC));
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

      this._labelsVisibility = this.getConfig('m44Labels', 1);
      $('m44-board-wrapper').dataset.labels = this._labelsVisibility;
      dojo.connect($('m44-labels-settings'), 'click', () => this.toggleLabelsVisibility());
    },

    removeClassNameOfCells(className) {
      let cells = [...$('m44-board').getElementsByClassName(className)];
      cells.forEach((cell) => cell.classList.remove(className));
    },

    toggleLabelsVisibility() {
      this._labelsVisibility = 1 - this._labelsVisibility;
      $('m44-board-wrapper').dataset.labels = this._labelsVisibility;
      localStorage.setItem('m44Labels', this._labelsVisibility);
    },

    incBoardScale(delta) {
      this._boardScale += delta;
      document.documentElement.style.setProperty('--memoirBoardScale', this._boardScale);
      //TODO localStorage.setItem('agricolaCardScale', scale);
    },

    getBackgroundTile(face, dim, x, y) {
      let tile = 0;
      if (face == 'winter') {
        type = getRandomInt(30, 35);
      } else if (face == 'desert') {
        tile = getRandomInt(19, 24);
      } else if (face == 'country') {
        tile = getRandomInt(9, 12);
      } else if (face == 'beach') {
        let b = dim.y - y;
        if (b < 2) tile = 27 + (x % 2);
        else if (b < 3) tile = 25 + (x % 2);
        else if (b < 4) tile = 6 + (x % 2);
        else if (b < 5) tile = 4 + (x % 2);
        else if (b < 6) tile = 2 + (x % 2);
        else if (b < 7) tile = 0 + (x % 2);
        else tile = getRandomInt(9, 12);
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

    tplTerrainTile(terrain) {
      let tile = TERRAINS.findIndex((t) => t == terrain.tile);
      let rotation = terrain.rotate ? 6 : 0;
      if (terrain.orientation != 1) {
        let nbrRotation = 3;
        if (TERRAINS_ROTATIONS[6].includes(terrain.tile)) nbrRotation = -6;
        if (TERRAINS_ROTATIONS[2].includes(terrain.tile)) nbrRotation = 2;
        rotation += ((terrain.orientation - 1) * 12) / nbrRotation + 12;
      }
      rotation = rotation % 12;
      return `<div class="hex-grid-content hex-grid-terrain" data-tile="${tile}" data-rotation="${rotation}"></div>`;
    },

    tplObstacleTile(obstacle) {
      let tile = OBSTACLES.findIndex((t) => t == obstacle.tile);
      let rotation = obstacle.rotate ? 6 : 0;
      if (obstacle.orientation != 1) {
        let angle = OBSTACLES_ROTATION[obstacle.tile] / -30;
        rotation += (obstacle.orientation - 1) * angle + 12;
      }
      rotation = rotation % 12;
      return `<div id="obstacle-${obstacle.id}" class="hex-grid-content hex-grid-obstacle" data-tile="${tile}" data-rotation="${rotation}"></div>`;
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

    tplBoardMedal(medal) {
      const SPRITES = ['medal1', 'medal2', 'medal4', 'medal5', 'medal6', 'medal7', 'medal8', 'medal9'];
      let sprite = SPRITES.findIndex((t) => t == medal.sprite);

      return `
      <div id='board-medal-${medal.id}' class="board-medal" data-team="${medal.team}" data-sprite="${sprite}"></div>`;
    },

    notif_addObstacle(n) {
      debug('Notif: adding obstacle', n);
      let terrain = n.args.terrain;
      let cellC = $(`cell-background-${terrain.x}-${terrain.y}`);
      terrain.rotate = this._isRotated;
      this.place('tplObstacleTile', terrain, cellC);
    },

    notif_removeObstacle(n) {
      debug('Notif: removing obstacle', n);
      $('obstacle-' + n.args.terrainId).remove();
    },

    //////////////////////////////////////////
    //    _   _       _ _
    //   | | | |_ __ (_) |_ ___
    //   | | | | '_ \| | __/ __|
    //   | |_| | | | | | |_\__ \
    //    \___/|_| |_|_|\__|___/
    //
    //////////////////////////////////////////

    tplUnit(unit) {
      let classNames = [];
      if (unit.activationCard > 0) classNames.push('activated');
      if (unit.onTheMove) classNames.push('onTheMove');

      return `
      <div id="unit-${unit.id}" class="m44-unit ${classNames.join(' ')}" data-figures="${unit.figures}" data-type="${
        unit.type
      }" data-nation="${unit.nation}" data-orientation="${unit.orientation}">
        <div class='m44-meeples-container'>
          <div class="m44-unit-meeple"></div>
          <div class="m44-unit-meeple"></div>
          <div class="m44-unit-meeple"></div>
          <div class="m44-unit-meeple"></div>
          <div class="m44-unit-meeple"></div>
        </div>
      </div>`;
    },

    makeUnitsSelectable(units, callback, checkCallback, className = 'selected') {
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
      if (sections) {
        fillings = fillings.filter(
          (filling) => filling[0] <= sections[0] && filling[1] <= sections[1] && filling[2] <= sections[2],
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
        let die = this.place('tplDice', { result, status : 'preAnimation' }, o);
        this.wait(i * 100).then(() => {
          die.querySelector('.m44-dice-wrapper').classList.add('animated');
          dojo.fadeOut({
            node: die,
            duration: 800,
            delay: 2000,
          }).play();
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
  });
});
