<?php

/*
 * ST constants
 */
const ST_GAME_SETUP = 1;
const ST_M44 = 2;
const ST_PREPARE_TURN = 3;
const ST_PLAY_CARD = 4;
const ST_SELECT_UNIT = 5;
const ST_MOVE_UNIT = 6;
const ST_ATTACK = 7;
const ST_PRE_AMBUSH = 8;
const ST_OPPONENT_AMBUSH = 9;
const ST_POST_AMBUSH = 10;
const ST_ATTACK_THROW = 11;
const ST_ATTACK_RESOLVE = 12;
const ST_END_ROUND = 13;
const ST_AMBUSH_ATTACK = 14;
const ST_AMBUSH_RESOLVE = 15;
const ST_CHANGE_PLAYER = 95;

const ST_END_GAME = 99;

/*
 * Card constants
 */
const STANDARD_DECK = 'STANDARD';
const BREAKTHROUGH_DECK = 'BRKTHRU'; // TODO : check from editor
const OVERLORD_DECK = 'OVERLORD'; // TODO : check from editor

const INFINITY = 100;
const LEFT_SECTION = 1;
const CENTER_SECTION = 2;
const RIGHT_SECTION = 3;

const CARD_RECON = 0;
const CARD_PROBE = 1;
const CARD_ATTACK = 2;
const CARD_ASSAULT = 3;
const CARD_GENERAL_ADVANCE = 4;
const CARD_PINCER_MOVE = 5;
const CARD_RECON_IN_FORCE = 6;

const CARD_CLASSES = [
  CARD_RECON => 'Recon',
  CARD_PROBE => 'Probe',
  CARD_ATTACK => 'Attack',
  CARD_ASSAULT => 'Assault',
  CARD_GENERAL_ADVANCE => 'GeneralAdvance',
  CARD_PINCER_MOVE => 'PincerMove',
  CARD_RECON_IN_FORCE => 'ReconInForce',
];

/*
 * Game options
 */

/*
 * Stats
 */

/*
 * Troops
 */
const INFANTRY = 1;
const ARMOR = 2;
const ARTILLERY = 3;

/*
 * Terrains
 */

const WINTER_FACE = 'WINTER';
const BEACH_FACE = 'BEACH';
const COUNRY_FACE = 'COUNRY'; // TODO : check from editor
const DESERT_FACE = 'DESERT'; // TODO : check from editor

const TERRAIN_CLASSES = [
  'woods' => 'Woods',
  'hedgerow' => 'Hedgerows',
  'palmtrees' => 'PalmForest',
  'pjungle' => 'Jungle',
  'price' => 'RicePaddies',
  'pbeach' => 'Beach',
  'wforest' => 'WinterForest',
  'marshes' => 'Marshes',
  'wmarshes' => 'WinterMarshes',
  'hills' => 'Hills',
  'highground' => 'HighGround',
  'mountain' => 'Mountain',
  'wadi' => 'Wadi',
  'wcurve' => 'CurvedWadi',
  'ptrenches' => 'Trench',
  'cravine' => 'CurvedGully',
  'ravine' => 'Gully',
  'dhill' => 'DesertHill',
  'whill' => 'WinterHill',
  'wtrenches' => 'WinterTrenches',
  'whillforest' => 'WinterForestonaHill',
  'wravine' => 'Ravine',
  'dridge' => 'ErgorRidge',
  'descarpment' => 'Escarpment',
  'pcave' => 'Cave',
  'pmcave' => 'MountainCave',
  'buildings' => 'Town',
  'depot' => 'SupplyDepot',
  'powerplant' => 'PowerPlant',
  'camp' => 'PrisonerCamp',
  'radar' => 'Radar',
  'lighthouse' => 'Lighthouse',
  'barracks' => 'Barracks',
  'cemetery' => 'Cemetery',
  'church' => 'Church',
  'factory' => 'FactoryComplex',
  'fortress' => 'Fortress',
  'bled' => 'NorthAfricanVillage',
  'pvillage' => 'PacificVillage',
  'phospital' => 'FieldHospital',
  'pheadquarter' => 'HQSupplyTents',
  'wruins' => 'CityRuins',
  'wvillage' => 'WinterVillage',
  'wfactory' => 'WinterFactoryComplex',
  'whillvillage' => 'WinterVillageonaHill',
  'dcamp' => 'HQSupplyTents',
  'river' => 'River',
  'curve' => 'Curves',
  'riverY' => 'RiverJunction',
  'riverFL' => 'RiverForkLeft',
  'riverFR' => 'RiverForkRight',
  'pond' => 'Headwater',
  'dam' => 'Dam',
  'oasis' => 'Oasis',
  'ppier' => 'Pier',
  'pmouth' => 'Rivermouth',
  'lakeA' => 'Lake2sides',
  'lakeB' => 'Lake3sides',
  'lakeC' => 'Lake3sidesandriver',
  'wriver' => 'FrozenRiver',
  'wriverFR' => 'FrozenRiverForkRight',
  'wcurved' => 'FrozenRiver',
  'coast' => 'Coastline',
  'coastcurve' => 'Coastline',
  'road' => 'Road',
  'roadcurve' => 'Curve',
  'roadFL' => 'RoadForkLeft',
  'roadFR' => 'RoadForkRight',
  'roadX' => 'RoadCrossing',
  'roadY' => 'RoadJunction',
  'hillroad' => 'RoadoveraHill',
  'hillcurve' => 'CurveoveraHill',
  'droad' => 'DesertRoad',
  'droadX' => 'DesertRoadCrossing',
  'droadcurve' => 'DesertCurve',
  'droadFL' => 'DesertRoadForkLeft',
  'droadFR' => 'DesertRoadForkRight',
  'wroad' => 'WinterRoad',
  'wroadcurve' => 'WinterCurve',
  'wroadFL' => 'WinterRoadForkLeft',
  'wroadFR' => 'WinterRoadForkRight',
  'wroadX' => 'WinterRoadCrossing',
  'wroadY' => 'WinterRoadJunction',
  'airfield' => 'Airfield',
  'airfieldX' => 'AirfieldCenter',
  'pairfield' => 'JungleAirfield',
  'pairfieldX' => 'JungleAirfieldCenter',
  'cairfield' => 'CountryAirfield',
  'wairfield' => 'WinterAirfield',
  'dairfieldX' => 'DesertAirfieldCenter',
  'dairfield' => 'DesertAirfield',
  'rail' => 'Railroad',
  'railcurve' => 'CurvedRailroad',
  'railFL' => 'RailForkLeft',
  'railFR' => 'RailForkRight',
  'railX' => 'RailCrossing',
  'station' => 'TrainStation',
  'railroad' => 'RailroadRoadCrossing',
  'wrail' => 'WinterRailroad',
  'wrailcurve' => 'WinterCurvedRailroad',
  'wrailFR' => 'WinterRailForkRight',
  'wrailroad' => 'WinterRailroadRoadCrossing',
  'wchurch' => 'WinterChurch',
  'wcastle' => 'WinterCastle',

  'bunker' => 'Bunker',
  'pbunker' => 'PBunker',
  'casemate' => 'FieldBunker',
  'pcarrier' => 'AircraftCarrier',
  'pdestroyer' => 'Destroyership',
  'barge' => 'LandingCrafts',
  'wbunker' => 'WinterFieldBunker',
  'dbunker' => 'DesertBunker',
  'ford' => 'Ford',
  'roadblock' => 'Roadblock',
  'droadblock' => 'DesertRoadblock',
  'wroadblock' => 'WinterRoadblock',
  'pontoon' => 'Pontoon',
  'wpontoon' => 'WinterPontoon',
  'dragonteeth' => 'DragonsTeeth',
  'railbridge' => 'RailroadBridge',
  'wrailbridge' => 'WinterRailroadBridge',
  'bridge' => 'Bridge',
  'pbridge' => 'Ropebridge',
  'brkbridge' => 'BrokenBridge',
  'wbridge' => 'WinterBridge',
  'wagon' => 'Wagon',
  'loco' => 'Locomotive',
  'abatis' => 'Abatis',
  'wire' => 'Barbedwire',
  'sand' => 'SandBags',
  'hedgehog' => 'Hedgehog',
];

const TROOP_CLASSES = [
  'inf2' => 'SpecialForces',
  'tank2' => 'EliteArmor',
  'inf' => 'Infantry',
  'tank' => 'Armor',
  'gun' => 'Artillery',
];

const TROOP_FIGURES = [
  'inf2' => 4,
  'tank2' => 3,
  'inf' => 4,
  'tank' => 3,
  'gun' => 2,
];
