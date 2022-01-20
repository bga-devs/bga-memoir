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
        if (hexagon.rect_terrain) {
          this.place('tplObstacleTile', hexagon.rect_terrain, cellC);
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
      let type = TERRAINS.findIndex((t) => t == terrain.name);
      let rotation = 0;
      if (terrain.orientation) {
        let nbrRotation = 3;
        if (TERRAINS_ROTATIONS[6].includes(terrain.name)) nbrRotation = -6;
        if (TERRAINS_ROTATIONS[2].includes(terrain.name)) nbrRotation = 2;
        rotation = (((terrain.orientation - 1) * 12) / nbrRotation + 12) % 12;
      }

      return `<div class="hex-grid-content hex-grid-terrain" data-type="${type}" data-rotation="${rotation}"></div>`;
    },

    tplObstacleTile(obstacle) {
      let type = OBSTACLES.findIndex((t) => t == obstacle.name);
      let rotation = 0;
      if (obstacle.orientation) {
        let angle = OBSTACLES_ROTATION[obstacle.name] / -30;
        rotation = ((obstacle.orientation - 1) * angle + 12) % 12;
      }
      return `<div class="hex-grid-content hex-grid-obstacle" data-type="${type}" data-rotation="${rotation}"></div>`;
    },
  });
});
