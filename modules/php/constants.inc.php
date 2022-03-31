<?php

/*
 * ST constants
 */
const ST_GAME_SETUP = 1;
const ST_START_TABLE = 2;
const ST_UPLOAD_SCENARIO = 4;
const ST_NEW_ROUND = 3;
const ST_PREPARE_TURN = 8;
const ST_COMMISSAR = 9;
const ST_PLAY_COMMISSAR = 10;
const ST_PLAY_CARD = 11;
const ST_ORDER_UNITS = 12;
const ST_MOVE_UNITS = 13;
const ST_ATTACK = 14;
const ST_OPPONENT_AMBUSH = 15;
const ST_AMBUSH_RESOLVE = 16;
const ST_ATTACK_THROW = 17;
const ST_ATTACK_RETREAT = 18;
const ST_TAKING_GROUND = 19;
const ST_ARMOR_OVERRUN = 20;
const ST_DRAW = 21;
const ST_DRAW_CHOICE = 22;
const ST_END_TURN = 23;
const ST_DESERT_MOVE = 24; // desert rules
const ST_BATTLE_BACK = 25; // British command

const ST_DIG_IN = 30;
const ST_PRE_MOVE_AGAIN = 31; // Behind ennemy lines
const ST_MOVE_AGAIN = 32; // Behind ennemy lines
const ST_FINEST_HOUR_ROLL = 33; // Finest Hour
const ST_FINEST_HOUR_ORDER = 34; // Finest Hour
const ST_AIRPOWER_TARGET = 35; // Air power targeting
const ST_BARRAGE_TARGET = 36;
const ST_MEDICS_TARGET = 37;
const ST_COUNTER_ATTACK = 38;

const ST_OVERLORD_PLAY_CARD = 40;
const ST_OVERLORD_SELECT_UNIT = 41;
const ST_OVERLORD_MOVE_UNIT = 42;
const ST_OVERLORD_ATTACK = 43;

const ST_AIR_DROP = 50;

const ST_CHANGE_ACTIVE_PLAYER = 95;

const ST_END_OF_GAME = 98;
const ST_END_GAME = 99;

const AXIS = 'AXIS';
const ALLIES = 'ALLIES';

/*
 * Card constants
 */
const STANDARD_DECK = 'STANDARD';
const BREAKTHROUGH_DECK = 'BRKTHRU';
const OVERLORD_DECK = 'OVERLORD';

const INFINITY = 100;
const LEFT_SECTION = 1;
const CENTER_SECTION = 2;
const RIGHT_SECTION = 3;

const CARD_RECON = 0; // 3 VERSIONS
const CARD_PROBE = 3; // 3 VERSIONS
const CARD_ATTACK = 6; // 3 VERSIONS
const CARD_GENERAL_ADVANCE = 9;
const CARD_RECON_IN_FORCE = 10;
const CARD_PINCER_MOVE = 11;
const CARD_ASSAULT = 12; // 3 VERSIONS

const CARD_AIR_POWER = 20;
const CARD_AMBUSH = 21;
const CARD_ARMOR_ASSAULT = 22; // TWO VERSIONS
const CARD_ARTILLERY_BOMBARD = 24;
const CARD_BARRAGE = 25;
const CARD_BEHIND_LINES = 26;
const CARD_CLOSE_ASSAULT = 27;
const CARD_COUNTER_ATTACK = 28; // TWO VERSIONS
const CARD_DIG_IN = 30;
const CARD_DIRECT_FROM_HQ = 31; // TWO VERSIONS
const CARD_FIREFIGHT = 33;
const CARD_INFANTRY_ASSAULT = 34; // TWO VERSIONS
const CARD_MEDICS = 36;
const CARD_MOVE_OUT = 37; // TWO VERSIONS
const CARD_FINEST_HOUR = 39;

const CARD_CLASSES = [
  CARD_RECON => 'Recon',
  CARD_PROBE => 'Probe',
  CARD_ATTACK => 'Attack',
  CARD_ASSAULT => 'Assault',
  CARD_GENERAL_ADVANCE => 'GeneralAdvance',
  CARD_PINCER_MOVE => 'PincerMove',
  CARD_RECON_IN_FORCE => 'ReconInForce',
  CARD_AIR_POWER => 'AirPower',
  CARD_AMBUSH => 'Ambush',
  CARD_ARMOR_ASSAULT => 'ArmorAssault',
  CARD_ARTILLERY_BOMBARD => 'ArtilleryBombard',
  CARD_BARRAGE => 'Barrage',
  CARD_BEHIND_LINES => 'BehindEnemyLines',
  CARD_CLOSE_ASSAULT => 'CloseAssault',
  CARD_COUNTER_ATTACK => 'CounterAttack',
  CARD_DIG_IN => 'DigIn',
  CARD_DIRECT_FROM_HQ => 'DirectFromHQ',
  CARD_FIREFIGHT => 'Firefight',
  CARD_INFANTRY_ASSAULT => 'InfantryAssault',
  CARD_MEDICS => 'MedicsAndMechanics',
  CARD_MOVE_OUT => 'MoveOut',
  CARD_FINEST_HOUR => 'FinestHour',
];

/*
 * Game options
 */
const OPTION_DURATION = 100;
const OPTION_DURATION_TWO_WAYS = 1;
const OPTION_DURATION_ONE_WAY = 2;

const OPTION_MODE = 101;
const OPTION_MODE_STANDARD = 1;
const OPTION_MODE_BREAKTHROUGH = 2;
const OPTION_MODE_OVERLORD = 3;

const OPTION_SCENARIO_TYPE = 102;
const OPTION_SCENARIO_OFFICIAL = 1;
const OPTION_SCENARIO_CUSTOM = 2;

const OPTION_SCENARIO_STANDARD = 103;
const OPTION_SCENARIO_BREAKTHROUGH = 104;
const OPTION_SCENARIO_OVERLORD = 105;

const OPTION_ONE_WAY_SIDE = 106;

/*
 * Dice
 */
const DICE_INFANTRY = 0;
const DICE_ARMOR = 1;
const DICE_GRENADE = 2;
const DICE_FLAG = 3;
const DICE_STAR = 4;

/*
 * Units
 */
const INFANTRY = 1;
const ARMOR = 2;
const ARTILLERY = 3;
const ALL_UNITS = 4;

/*
 * Medals / Tokens
 */
const MEDAL_ELIMINATION = 1;
const MEDAL_POSITION = 2;
const MEDAL_SUDDEN_DEATH = 3;

const TOKEN_MEDAL = 1;
const TOKEN_MINE = 2;
const TOKEN_TARGET = 3;
const TOKEN_CAMOUFLAGE = 4;
const TOKEN_EXIT_MARKER = 5;

/*
 * Terrains
 */

const WINTER_FACE = 'WINTER';
const BEACH_FACE = 'BEACH';
const COUNRY_FACE = 'COUNRY';
const DESERT_FACE = 'DESERT';

const TERRAIN_CLASSES = [
  'beach' => 'Beach',
  'bunker' => 'Bunker',
  'forest' => 'Forest',
  'hedgerow' => 'Hedgerow',
  'hedgehog' => 'Hedgehog',
  'hill' => 'Hill',
  'ocean' => 'Ocean',
  'river' => 'River',
  'bridge' => 'Bridge',
  'bridgesection' => 'BridgeSection',
  'sand' => 'Sandbag',
  'cliff' => 'Cliff',
  'bluff' => 'SeaBluff',
  'seawall' => 'SeaWall',
  'steephill' => 'SteepHill',
  'village' => 'Village',
  'wire' => 'Wire',
  'airfield' => 'AirField',
  'barack' => 'Barrack',
  'cemetery' => 'Cemetery',
  'church' => 'Church',
  'dam' => 'Dam',
  'factory' => 'FactoryComplex',
  'fieldbunker' => 'FieldBunker',
  // Flooded Fields
  'fortress' => 'Fortress',
  'highground' => 'HighGround',
  'lake' => 'Lake',
  'lighthouse' => 'Lighthouse',
  'marsh' => 'Marsh',
  'minefield' => 'Minefield',
  'mountain' => 'Mountain',
  'oasis' => 'Oasis',
  'palmforest' => 'PalmForest',
  'poontoon' => 'Poontoon',
  'powerplant' => 'PowerPlant',
  'prisoncamp' => 'PrisonCamp',
  'radarstation' => 'RadarStation',
  'railroadbridge' => 'RailroadBridge',
  'rail' => 'Rail',
  'railstation' => 'RailStation',
  'roadblock' => 'RoadBlock',
  'ford' => 'Ford',
  'road' => 'Road',
  'roadhill' => 'RoadHill',
  'supplydepot' => 'SupplyDepot',
  'wadi' => 'Wadi',

  'ruins' => 'Ruins',
  'dragonteeth' => 'DragonTeeth',
  'frozenriver' => 'FrozenRiver',
  'hillforest' => 'HillForest',
  'hillvillage' => 'HillVillage',
  'ravine' => 'Ravine',
  'trenches' => 'Trenches',

  'coastline' => 'CoastLine',
  'erg' => 'Erg',
  'escarpment' => 'Escarpment',
  'hq' => 'HQ',
];

const TROOP_CLASSES = [
  'inf2' => 'SpecialForces',
  'tank2' => 'EliteArmor',
  'inf' => 'Infantry',
  'tank' => 'Armor',
  'gun' => 'Artillery',
  // type + badge number if non decorative
  'inf2_3' => 'FrenchResistance',
  'gun_5' => 'BigGun',
  'inf2_4' => 'CombatEngineer',
  'inf2_6' => 'CombatEngineer',
  'inf2_8' => 'CombatEngineer',
  'inf2_12' => 'CombatEngineer',
  'inf2_28' => 'CombatEngineer',
  'inf_26' => 'Sniper',
  'inf_27' => 'Sniper',
  'inf_29' => 'Cavalry',
  'inf2_30' => 'SkiTroop',
  'inf_37' => 'AntiTank',
];

const TROOP_BADGE_MAPPING = ['FRENCH_RESISTANCE' => 'badge3'];

// badge mapping nation
const TROOP_NATION_MAPPING = ['38' => 'it'];

/******************
 ****** STATS ******
 ******************/
const STAT_SCENARIO_ID = 200;

const STAT_TEAM_FIRST_ROUND = 10;
const STAT_STATUS_FIRST_ROUND = 11;
const STAT_MEDAL_FIRST_ROUND = 12;
const STAT_INF_UNIT_FIRST_ROUND = 13;
const STAT_ARMOR_UNIT_FIRST_ROUND = 14;
const STAT_ARTILLERY_UNIT_FIRST_ROUND = 15;
const STAT_INF_FIG_FIRST_ROUND = 16;
const STAT_ARMOR_FIG_FIRST_ROUND = 17;
const STAT_ARTILLERY_FIG_FIRST_ROUND = 18;

const STAT_TEAM_SECOND_ROUND = 30;
const STAT_STATUS_SECOND_ROUND = 31;
const STAT_MEDAL_SECOND_ROUND = 32;
const STAT_INF_UNIT_SECOND_ROUND = 33;
const STAT_ARMOR_UNIT_SECOND_ROUND = 34;
const STAT_ARTILLERY_UNIT_SECOND_ROUND = 35;
const STAT_INF_FIG_SECOND_ROUND = 36;
const STAT_ARMOR_FIG_SECOND_ROUND = 37;
const STAT_ARTILLERY_FIG_SECOND_ROUND = 38;

const STAT_DICE_COUNT = 50;
const STAT_DICE_INF = 51;
const STAT_DICE_ARMOR = 52;
const STAT_DICE_GRENADE = 53;
const STAT_DICE_STAR = 54;
const STAT_DICE_FLAG = 55;

const alphabet = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm'];
