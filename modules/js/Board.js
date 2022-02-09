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
    setupBoard(board, rotate, bottomTeam) {
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
          let type = this.getBackgroundType(face, dim, x, y);
          let cellC = this.place('tplBoardBackgroundCell', { x: col, y, type, rotate }, 'm44-board-terrains');
          cellC.style.gridRow = 3 * realY + 1 + ' / span 4';
          cellC.style.gridColumn = realX + 1 + ' / span 2';
          board.grid[col][row].terrains.forEach((terrain) => {
            let tplName = TERRAINS.includes(terrain.type) ? 'tplTerrainTile' : 'tplObstacleTile';
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

          let units = board.grid[col][row].units;
          if (units.length > 0) {
            units.forEach((unit) => {
              unit.orientation = bottomTeam != (ALLIES_NATIONS.includes(unit.nation) ? 'ALLIES' : 'AXIS') ? 1 : 0;
              this.place('tplUnit', unit, `cell-${col}-${y}`);
            });
          }
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
        o.style.gridRow = '1 / span ' + 3 * (dim.y + 1);
        o.style.gridColumn = x + ' / span 1';
      });

      // Add line of sight
      dojo.place('<div id="lineOfSight"></div>', 'm44-board-units');

      this._boardScale = 1; // TODO localStorage
      dojo.connect($('m44-board-zoom-in'), 'click', () => this.incBoardScale(0.1));
      dojo.connect($('m44-board-zoom-out'), 'click', () => this.incBoardScale(-0.1));

      this._labelsVisibility = this.getConfig('m44Labels', 1);
      $('m44-board-wrapper').dataset.labels = this._labelsVisibility;
      dojo.connect($('m44-labels-settings'), 'click', () => this.toggleLabelsVisibility());
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

    getBackgroundType(face, dim, x, y) {
      let type = 0;
      if (face == 'winter') {
        type = getRandomInt(30, 35);
      } else if (face == 'desert') {
        type = getRandomInt(19, 24);
      } else if (face == 'country') {
        type = getRandomInt(9, 12);
      } else if (face == 'beach') {
        let b = dim.y - y;
        if (b < 2) type = 27 + (x % 2);
        else if (b < 3) type = 25 + (x % 2);
        else if (b < 4) type = 6 + (x % 2);
        else if (b < 5) type = 4 + (x % 2);
        else if (b < 6) type = 2 + (x % 2);
        else if (b < 7) type = 0 + (x % 2);
        else type = getRandomInt(9, 12);
      }
      return type;
    },

    tplBoardBackgroundCell(cell) {
      let rotation = cell.rotate ? 6 : 0;
      return `<li class="hex-grid-item" id="cell-background-${cell.x}-${cell.y}">
      <div class="hex-grid-content hex-grid-background" data-type="${cell.type}" data-rotation="${rotation}"></div>
    </li>`;
    },

    tplBoardCell(cell) {
      return `<li class="hex-grid-item hex-cell-container" id="cell-container-${cell.x}-${cell.y}">
      <div class='hex-cell' id="cell-${cell.x}-${cell.y}"></div>
    </li>`;
    },

    tplTerrainTile(terrain) {
      let type = TERRAINS.findIndex((t) => t == terrain.type);
      let rotation = terrain.rotate ? 6 : 0;
      if (terrain.orientation != 1) {
        let nbrRotation = 3;
        if (TERRAINS_ROTATIONS[6].includes(terrain.type)) nbrRotation = -6;
        if (TERRAINS_ROTATIONS[2].includes(terrain.type)) nbrRotation = 2;
        rotation += ((terrain.orientation - 1) * 12) / nbrRotation + 12;
      }
      rotation = rotation % 12;
      return `<div class="hex-grid-content hex-grid-terrain" data-type="${type}" data-rotation="${rotation}"></div>`;
    },

    tplObstacleTile(obstacle) {
      let type = OBSTACLES.findIndex((t) => t == obstacle.type);
      let rotation = obstacle.rotate ? 6 : 0;
      if (obstacle.orientation != 1) {
        let angle = OBSTACLES_ROTATION[obstacle.type] / -30;
        rotation += (obstacle.orientation - 1) * angle + 12;
      }
      rotation = rotation % 12;
      return `<div class="hex-grid-content hex-grid-obstacle" data-type="${type}" data-rotation="${rotation}"></div>`;
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

    //////////////////////////////////////////
    //    _____
    //   |_   _| __ ___   ___  _ __  ___
    //     | || '__/ _ \ / _ \| '_ \/ __|
    //     | || | | (_) | (_) | |_) \__ \
    //     |_||_|  \___/ \___/| .__/|___/
    //                        |_|
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
    },

    /**
     * getMinFillingOfSections: handle units that are on two sections
     */
    getMinFillingOfSections() {
      let fillings = [[0, 0, 0]];
      this._selectedUnits.forEach((unitId) => {
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
      let minFilling = [10, 10, 10];
      fillings.forEach((filling) => {
        for (let i = 0; i < 3; i++) {
          minFilling[i] = Math.min(minFilling[i], filling[i]);
        }
      });
      return minFilling;
    },

    ////////////////////////////
    //    ____  _
    //   |  _ \(_) ___ ___
    //   | | | | |/ __/ _ \
    //   | |_| | | (_|  __/
    //   |____/|_|\___\___|
    /////////////////////////////
    rollDice(cell, results) {
      let o = $(`cell-${cell.x}-${cell.y}`);
      results.forEach((result) => {
        let die = this.place('tplDice', { result, animated: true }, o);
        die.style.transform = 'translateX(100%)';
      });
    },

    tplDice(dice) {
      return `<div class="m44-dice-wrapper ${dice.animated ? 'animated' : ''}" data-result="${dice.result}">
        <div class="m44-dice-shadow"></div>
        <div class="m44-dice"></div>
      </div>`;
    },
  });
});
