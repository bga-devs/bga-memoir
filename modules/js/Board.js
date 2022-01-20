define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
  function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
  }

  // prettier-ignore
  const TERRAINS = ['airfield','airfieldX1','airfieldX','barracks','bled','cairfield','camp','cemetery','church','coastcurve','coast','cravine','curve','dairfield','dairfieldX','dam','dcamp','depot','descarpment','dhill','dridge','droadcurve','droadFL','droadFR','droad','droadX','factory','forest','fortress','hedgerow','highground','hillcurve','hillroad','hills','lakeA','lakeB','lakeC','lighthouse','marshes','mountain','oasis','pairfield','pairfieldX','palmtrees','pbeach','pcave','pheadquarter','phospital','pjungle','pmcave','pmouth','pond','powerplant','ppier','price','ptrenches','pvillage','radar','railcurve','railFL','railFR','rail','railroad','railX','ravine','riverFL','riverFR','river','riverY','roadcurve','roadFL','roadFR','road','roadX','roadY','station','village','wadi','wairfield','wcurved','wcurve','wfactory','wforest','whillforest','whill','whillvillage','wmarshes','wravine','wriver','wruins','wtrenches','wvillage'];

  // prettier-ignore
  const OBSTACLES = ['abatis','barge','bridge','brkbridge','bunker','casemate','dbunker','dragonteeth','droadblock','ford','hedgehog','loco','pbridge','pbunker','pcarrier','pdestroyer','pontoon','railbridge','roadblock','sand','wagon','wbunker','wire'];

  return declare('memoir.board', null, {
    setupBoard(board) {
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

          let type = 0;
          if (face == 'winter') {
            type = getRandomInt(30, 35);
          } else if (face == 'beach') {
            if (y < 3) type = getRandomInt(9, 12);
            else if (y < 4) type = getRandomInt(0, 1);
            else if (y < 5) type = getRandomInt(2, 3);
            else if (y < 6) type = getRandomInt(4, 5);
            else if (y < 7) type = getRandomInt(6, 7);
            else if (y < 8) type = getRandomInt(25, 26);
            else type = getRandomInt(27, 28);
          }
          this.place('tplBoardCell', { x: col, y, type }, 'm44-board');

          let cellC = $(`cell-container-${col}-${y}`);
          cellC.style.gridRow = 3 * y + 1 + ' / span 4';
          cellC.style.gridColumn = col + 1 + ' / span 2';
        }
      }

      // Add terrains tiles
      board.hexagons.forEach((hexagon) => {
        let cellC = $(`cell-container-${hexagon.col}-${hexagon.row}`);
        if (hexagon.terrain) {
          this.place('tplTerrainTile', hexagon.terrain, cellC);
        }
        if (hexagon.obstacle) {
          this.place('tplObstacleTile', hexagon.obstacle, cellC);
        }
      });
    },

    tplBoardCell(cell) {
      return `
    <li class="hex-grid-item" id="cell-container-${cell.x}-${cell.y}">
      <div class="hex-grid-content hex-grid-background" data-type="${cell.type}"></div>
      <div class="hex-grid-content hex-cell" id="cell-${cell.x}-${cell.y}"></div>
    </li>
      `;
    },

    tplTerrainTile(terrain) {
      let map = {
        buildings: 'village',
        woods: 'forest',
      };
      let name = terrain.name;
      if (map[name]) name = map[name];

      let type = TERRAINS.findIndex((t) => t == name);

      return `<div class="hex-grid-content hex-grid-terrain" data-type="${type}"></div>`;
    },

    tplObstacleTile(obstacle) {
      let type = OBSTACLES.findIndex((t) => t == obstacle.name);
      return `<div class="hex-grid-content hex-grid-obstacle" data-type="${type}"></div>`;
    },
  });
});
