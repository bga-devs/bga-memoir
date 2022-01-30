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

  const ALLIES_NATIONS = ['brit', 'us'];

  return declare('memoir.board', null, {
    setupBoard(board, rotate, bottomTeam) {
      // Get dimensions based on type
      let type = board.type.toLowerCase();
      $('m44-board').dataset.type = type;

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
          let col = 2 * x + (y % 2 == 0 ? 0 : 1);
          let row = y;

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
          this.place('tplBoardCell', { x: col, y, type, rotate }, 'm44-board');

          let cellC = $(`cell-background-${col}-${y}`);
          let realX = rotate ? 2 * size - col - (y % 2 == 0 ? 2 : 0) : col;
          let realY = rotate ? dim.y - y - 1 : y;
          cellC.style.gridRow = 3 * realY + 1 + ' / span 4';
          cellC.style.gridColumn = realX + 1 + ' / span 2';
          $(`cell-container-${col}-${y}`).style.gridArea = cellC.style.gridArea;

          // Add terrains tiles
          board.grid[col][row].terrains.forEach((terrain) => {
            let tplName = TERRAINS.includes(terrain.type) ? 'tplTerrainTile' : 'tplObstacleTile';
            terrain.rotate = rotate;
            this.place(tplName, terrain, cellC);
          });

          // Add units
          let unit = board.grid[col][row].unit;
          if (unit) {
            unit.orientation = bottomTeam != (ALLIES_NATIONS.includes(unit.nation) ? 'ALLIES' : 'AXIS') ? 1 : 0;
            this.place('tplUnit', unit, `cell-${col}-${y}`);
          }

          // Add labels
          let labels = board.grid[col][row].labels;
          if (labels.length > 0) {
            let label = labels.map((t) => _(t)).join('<br />');
            let area = 3 * realY + 4 + ' / ' + (+realX + 1) + ' / span ' + labels.length + ' / span 2';
            this.place('tplTileLabel', { label, area }, 'm44-board');
          }
        }
      }
    },

    tplBoardCell(cell) {
      let rotation = cell.rotate ? 6 : 0;
      return `
    <li class="hex-grid-item" id="cell-background-${cell.x}-${cell.y}">
      <div class="hex-grid-content hex-grid-background" data-type="${cell.type}" data-rotation="${rotation}"></div>
    </li>
    <li class="hex-grid-item hex-cell-container" id="cell-container-${cell.x}-${cell.y}">
      <div class='hex-cell' id="cell-${cell.x}-${cell.y}"></div>
    </li>
      `;
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

    tplTileLabel(label) {
      return `
      <div class="hex-label-container" style="grid-area:${label.area}">
        <div class="hex-label">${label.label}</div>
      </div>`;
    },

    tplUnit(unit) {
      return `
      <div id="unit-${unit.id}" class="memoir-unit" data-figures="${unit.figures}" data-type="${unit.type}" data-nation="${unit.nation}" data-orientation="${unit.orientation}">
        <div class="memoir-unit-meeple"></div>
        <div class="memoir-unit-meeple"></div>
        <div class="memoir-unit-meeple"></div>
        <div class="memoir-unit-meeple"></div>
        <div class="memoir-unit-meeple"></div>
      </div>`;
    },
  });
});
