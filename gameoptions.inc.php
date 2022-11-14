<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * memoir implementation : © Timothée Pecatte <tim.pecatte@gmail.com>, Vincent Toper <vincent.toper@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * gameoptions.inc.php
 *
 * memoir game options description
 *
 * !! It is not a good idea to modify this file when a game is running !!
 *
 */

namespace M44;

$game_options = [
  100 => [
    'name' => totranslate('Duration'),
    'values' => [
      1 => [
        'name' => clienttranslate('2 ways'),
        'description' => clienttranslate('The scenario will be played twice, switching the sides'),
      ],
      2 => [
        'name' => clienttranslate('1 way'),
        'tmdisplay' => clienttranslate('[1 way]'),
        'description' => clienttranslate('The scenario will only be played once'),
        'alpha' => true,
      ],
    ],
  ],

  101 => [
    'name' => totranslate('Mode'),
    'values' => [
      1 => [
        'name' => clienttranslate('Standard'),
        'description' => clienttranslate('Standard 1v1 mode'),
      ],
      2 => [
        'name' => clienttranslate('Breakthrough'),
        'tmdisplay' => clienttranslate('BKT'),
        'description' => clienttranslate('Breakthrough scenario'),
        'alpha' => true,
      ],
      // 3 => [
      //   'name' => clienttranslate('Overlord'),
      //   'tmpdisplay' => clienttranslate('Overlord'),
      //   'description' => clienttranslate('Overlord scenario'),
      // ],
    ],
    'startcondition' => [
      1 => [
        [
          'type' => 'maxplayers',
          'value' => 2,
          'message' => clienttranslate('Only Overlord mode can be played in teams'),
        ],
      ],
      2 => [
        [
          'type' => 'maxplayers',
          'value' => 2,
          'message' => clienttranslate('Only Overlord mode can be played in teams'),
        ],
      ],
    ],
  ],

  102 => [
    'name' => totranslate('Scenario type'),
    'values' => [
      1 => [
        'name' => clienttranslate('Official'),
        'description' => clienttranslate('Official DaysOfWonder scenario'),
      ],
      2 => [
        'name' => clienttranslate('From the front'),
        'tmdisplay' => clienttranslate('[fan-made]'),
        'description' => clienttranslate('Loading a m44 scenario made with the editor'),
        'alpha' => true,
        'premium' => true,
      ],
    ],
  ],

  103 => [
    'name' => clienttranslate('Scenario'),
    'displaycondition' => [
      0 => [
        'type' => 'otheroption',
        'id' => 101,
        'value' => 1,
      ],
      1 => [
        'type' => 'otheroptionisnot',
        'id' => 102,
        'value' => 2,
      ],
    ],
    'values' => [
      0 => [
        'name' => \clienttranslate('Random'),
        'tmdisplay' => \clienttranslate('[Random]'),
        'premium' => true,
      ],
      2 => [
        'name' => clienttranslate('[BB] [2] Pegasus Bridge'),
        'tmdisplay' => '[2]',
        'description' => clienttranslate('Pegasus Bridge'),
      ],
      3 => [
        'name' => clienttranslate('[BB] [3] Sainte-Mère-Eglise'),
        'tmdisplay' => '[3]',
        'description' => clienttranslate('Sainte-Mère-Eglise'),
      ],
      4 => [
        'name' => clienttranslate('[BB] [4] Sword Beach'),
        'tmdisplay' => '[4]',
        'description' => clienttranslate('Sword Beach'),
        'premium' => true,
      ],
      5 => [
        'name' => clienttranslate('[BB] [5] Mont Mouchet'),
        'tmdisplay' => '[5]',
        'description' => clienttranslate('Mont Mouchet'),
      ],
      7 => [
        'name' => clienttranslate('[BB] [7] Pointe-du-Hoc'),
        'tmdisplay' => '[7]',
        'description' => clienttranslate('Pointe-du-Hoc'),
      ],
      11 => [
        'name' => clienttranslate('[BB] [11] First Assault Wave'),
        'tmdisplay' => '[11]',
        'description' => clienttranslate('First Assault Wave'),
      ],
      15 => [
        'name' => clienttranslate('[BB] [15] Operation Cobra'),
        'tmdisplay' => '[15]',
        'description' => clienttranslate('Operation Cobra'),
        'premium' => true,
      ],
      16 => [
        'name' => clienttranslate('[BB] [16] Counter-attack on Mortain'),
        'tmdisplay' => '[16]',
        'description' => clienttranslate('Counter-attack on Mortain'),
      ],
      18 => [
        'name' => clienttranslate('[BB] [18] Montélimar'),
        'tmdisplay' => '[18]',
        'description' => clienttranslate('Montélimar'),
        'premium' => true,
      ],
      19 => [
        'name' => clienttranslate('[BB] [19] Vassieux'),
        'tmdisplay' => '[19]',
        'description' => clienttranslate('Vassieux'),
        'premium' => true,
      ],
      20 => [
        'name' => clienttranslate('[BB] [20] St Vith'),
        'tmdisplay' => '[20]',
        'description' => clienttranslate('St Vith'),
        'premium' => true,
      ],
      22 => [
        'name' => clienttranslate('[BB] [22] Liberation of Paris'),
        'tmdisplay' => '[22]',
        'description' => clienttranslate('Liberation of Paris'),
      ],
      23 => [
        'name' => clienttranslate('[BB] [23] Toulon'),
        'tmdisplay' => '[23]',
        'description' => clienttranslate('Toulon'),
        'premium' => true,
      ],
      24 => [
        'name' => clienttranslate('[BB] [24] Arracourt'),
        'tmdisplay' => '[24]',
        'description' => clienttranslate('Arracourt'),
        'premium' => true,
      ],
      25 => [
        'name' => clienttranslate('[BB] [25] Arnhem Bridge'),
        'tmdisplay' => '[25]',
        'description' => clienttranslate('Arnhem Bridge'),
      ],
      29 => [
        'name' => clienttranslate('[BB] [29] Saverne Gap'),
        'tmdisplay' => '[29]',
        'description' => clienttranslate('Saverne Gap'),
        'premium' => true,
      ],
      1302 => [
        'name' => clienttranslate('[TP] [1302] Nijmegen Bridges'),
        'tmdisplay' => '[1302]',
        'description' => clienttranslate('Nijmegen Bridges'),
        'premium' => true,
        'alpha' => true,
      ],
      // 1311 => [
      //   'name' => clienttranslate('[EF] [1311] Bug River'),
      //   'tmdisplay' => '[1311]',
      //   'description' => clienttranslate('Bug River'),
      //   'premium' => true,
      // ],
      1325 => [
        'name' => clienttranslate('[EF] [1325] Gates of Moscow'),
        'tmdisplay' => '[1325]',
        'description' => clienttranslate('Gates of Moscow'),
        'premium' => true,
      ],
      1340 => [
        'name' => clienttranslate('[TP] [1340] Across the River Roer'),
        'tmdisplay' => '[1340]',
        'description' => clienttranslate('Across the River Roer'),
        'premium' => true,
      ],
      1346 => [
        'name' => clienttranslate('[TP] [1346] Knightsbridge'),
        'tmdisplay' => '[1346]',
        'description' => clienttranslate('Knightsbridge'),
        'premium' => true,
      ],
      1365 => [
        'name' => clienttranslate('[EF] [1365] Ponyri'),
        'tmdisplay' => '[1365]',
        'description' => clienttranslate('Ponyri'),
        'premium' => true,
      ],
      1407 => [
        'name' => clienttranslate('[EF] [1407] Red Barricades Factory'),
        'tmdisplay' => '[1407]',
        'description' => clienttranslate('Red Barricades Factory'),
        'premium' => true,
      ],
      1409 => [
        'name' => clienttranslate('[EF] [1409] Suomussalmi'),
        'tmdisplay' => '[1409]',
        'description' => clienttranslate('Suomussalmi'),
        'premium' => true,
      ],
      // 1422 => [
      //   'name' => clienttranslate('[TP] [1422] Schwammenauel Dam'),
      //   'tmdisplay' => '[1422]',
      //   'description' => clienttranslate('Schwammenauel Dam'),
      //   'premium' => true,
      // ],
      1433 => [
        'name' => clienttranslate('[EF] [1433] Breakout at Klin'),
        'tmdisplay' => '[1433]',
        'description' => clienttranslate('Breakout at Klin'),
        'premium' => true,
      ],
      1434 => [
        'name' => clienttranslate('[EF] [1434] Breakout to Lisyanka'),
        'tmdisplay' => '[1434]',
        'description' => clienttranslate('Breakout to Lisyanka'),
        'premium' => true,
      ],
      // 23806 => [
      //   'name' => clienttranslate('[23806] [Battle of the Bulge] German counter-attack at Chaumont [FFM44]'),
      //   'tmdisplay' => '[23806]',
      //   'description' => clienttranslate('[Battle of the Bulge] German counter-attack at Chaumont [FFM44]'),
      // 'premium' => true,
      // ],

      1412 => [
        'name' => clienttranslate('[PT] [1412] Matanikau River'),
        'tmdisplay' => '[1412]',
        'description' => clienttranslate('Matanikau River'),
        'premium' => true,
      ],
      1413 => [
        'name' => clienttranslate('[PT] [1413] Slopes of Mount Austen'),
        'tmdisplay' => '[1413]',
        'description' => clienttranslate('Slopes of Mount Austen'),
        'premium' => true,
      ],
      1483 => [
        'name' => clienttranslate('[PT] [1483] The Meat Grinder'),
        'tmdisplay' => '[1483]',
        'description' => clienttranslate('The Meat Grinder'),
        'premium' => true,
      ],
      1487 => [
        'name' => clienttranslate('[PT] [1487] Guam Landings'),
        'tmdisplay' => '[1487]',
        'description' => clienttranslate('Guam Landings'),
        'premium' => true,
      ],
      1488 => [
        'name' => clienttranslate('[PT] [1488] Japanese Counterattack'),
        'tmdisplay' => '[1488]',
        'description' => clienttranslate('Japanese Counterattack'),
        'premium' => true,
      ],
      1518 => [
        'name' => clienttranslate('[PT] [1518] Sugar Loaf and Half Moon'),
        'tmdisplay' => '[1518]',
        'description' => clienttranslate('Sugar Loaf and Half Moon'),
        'premium' => true,
      ],
      1599 => [
        'name' => clienttranslate('[PT] [1599] Wake Island'),
        'tmdisplay' => '[1599]',
        'description' => clienttranslate('Wake Island'),
        'premium' => true,
      ],

      1324 => [
        'name' => clienttranslate('[MT] [1324] Dug in at Sidi Omar'),
        'tmdisplay' => '[1324]',
        'description' => clienttranslate('Dug in at Sidi Omar'),
        'premium' => true,
      ],

      4230 => [
        'name' => clienttranslate('[MT] [4230] Flanking Maneuver at Bir Hakeim'),
        'tmdisplay' => '[4230]',
        'description' => clienttranslate('Flanking Maneuver at Bir Hakeim'),
        'premium' => true,
      ],
      4231 => [
        'name' => clienttranslate('[MT] [4231] Panzers versus Grants'),
        'tmdisplay' => '[4231]',
        'description' => clienttranslate('Panzers versus Grants'),
        'premium' => true,
      ],
      4232 => [
        'name' => clienttranslate('[MT] [4232] 1st Armoured to the rescue'),
        'tmdisplay' => '[4232]',
        'description' => clienttranslate('1st Armoured to the rescue'),
        'premium' => true,
      ],
      4237 => [
        'name' => clienttranslate('[MT] [4237] Into the Cauldron'),
        'tmdisplay' => '[4237]',
        'description' => clienttranslate('Into the Cauldron'),
        'premium' => true,
      ],
      4241 => [
        'name' => clienttranslate('[MT] [4241] Escape via the Coastal Road'),
        'tmdisplay' => '[4241]',
        'description' => clienttranslate('Escape via the Coastal Road'),
        'premium' => true,
      ],
      4245 => [
        'name' => clienttranslate('[MT] [4245] Hellfire Pass'),
        'tmdisplay' => '[4245]',
        'description' => clienttranslate('Hellfire Pass'),
        'premium' => true,
      ],
      4246 => [
        'name' => clienttranslate('[MT] [4246] Sidi Rezegh Airfield'),
        'tmdisplay' => '[4246]',
        'description' => clienttranslate('Sidi Rezegh Airfield'),
        'premium' => true,
      ],
    ],
  ],
  104 => [
    'name' => clienttranslate('Scenario'),
    'displaycondition' => [
      0 => [
        'type' => 'otheroption',
        'id' => 101,
        'value' => 2,
      ],
      1 => [
        'type' => 'otheroptionisnot',
        'id' => 102,
        'value' => 2,
      ],
    ],
    'values' => [
      3129 => [
        'name' => clienttranslate('[3129] Operation Crusader'),
        'tmdisplay' => '[3129]',
        'description' => clienttranslate('Operation Crusader'),
        'premium' => true,
      ],
      4000 => [
        'name' => clienttranslate('[4000] The Tatsinskaya Raid'),
        'tmdisplay' => '[4000]',
        'description' => clienttranslate('The Tatsinskaya Raid'),
        'premium' => true,
      ],
      4625 => [
        'name' => clienttranslate('[4625] Coldstream Hill'),
        'tmdisplay' => '[4625]',
        'description' => clienttranslate('Coldstream Hill'),
        'premium' => true,
      ],
      4643 => [
        'name' => clienttranslate('[4643] Battle of Abbeville'),
        'tmdisplay' => '[4643]',
        'description' => clienttranslate('Battle of Abbeville'),
        'premium' => true,
      ],
      // 4683 => [
      //   'name' => clienttranslate('[4683] Manado Landings'),
      //   'tmdisplay' => '[4683]',
      //   'description' => clienttranslate('Manado Landings'),
      //   'premium' => true,
      // ],
      // 4717 => [
      //   'name' => clienttranslate('[4717] Nach Moskau!'),
      //   'tmdisplay' => '[4717]',
      //   'description' => clienttranslate('Nach Moskau!'),
      //   'premium' => true,
      // ],
      5142 => [
        'name' => clienttranslate('[5142] Counter-attack of the BEF'),
        'tmdisplay' => '[5142]',
        'description' => clienttranslate('Counter-attack of the BEF'),
        'premium' => true,
      ],
      // 5841 => [
      //   'name' => clienttranslate('[5841] The Surrender of Elster\'s column'),
      //   'tmdisplay' => '[5841]',
      //   'description' => clienttranslate('The Surrender of Elster\'s column'),
      //   'premium' => true,
      // ],
      // 5913 => [
      //   'name' => clienttranslate('[5913] Battle of Prokhorovka'),
      //   'tmdisplay' => '[5913]',
      //   'description' => clienttranslate('Battle of Prokhorovka'),
      //   'premium' => true,
      // ],
      6367 => [
        'name' => clienttranslate('[6367] Sword Beach'),
        'tmdisplay' => '[6367]',
        'description' => clienttranslate('Sword Beach'),
        'premium' => true,
      ],
      // 6369 => [
      //   'name' => clienttranslate('[6369] Operation Amherst'),
      //   'tmdisplay' => '[6369]',
      //   'description' => clienttranslate('Operation Amherst'),
      //    'premium' => true,
      // ],
      6370 => [
        'name' => clienttranslate('[6370] Battle of Alam el Halfa'),
        'tmdisplay' => '[6370]',
        'description' => clienttranslate('Battle of Alam el Halfa'),
        'premium' => true,
      ],
      // 6380 => [
      //   'name' => clienttranslate('[6380] Noville to Foy'),
      //   'tmdisplay' => '[6380]',
      //   'description' => clienttranslate('Noville to Foy'),
      //   'premium' => true,
      // ],
      // 6433 => [
      //   'name' => clienttranslate('[6433] Breakthrough at Mortain'),
      //   'tmdisplay' => '[6433]',
      //   'description' => clienttranslate('Breakthrough at Mortain'),
      //   'premium' => true,
      // ],
      // 6541 => [
      //   'name' => clienttranslate('[6541] Breakthrough to the Beach'),
      //   'tmdisplay' => '[6541]',
      //   'description' => clienttranslate('Breakthrough to the Beach'),
      //   'premium' => true,
      // ],
      // 18261 => [
      //   'name' => clienttranslate('[18261] [Ukraine] Soviet raid on Grigorevka'),
      //   'tmdisplay' => '[18261]',
      //   'description' => clienttranslate('[Ukraine] Soviet raid on Grigorevka'),
      //   'premium' => true,
      // ],
    ],
  ],
  105 => [
    'name' => clienttranslate('Scenario'),
    'displaycondition' => [
      0 => [
        'type' => 'otheroption',
        'id' => 101,
        'value' => 3,
      ],
      1 => [
        'type' => 'otheroptionisnot',
        'id' => 102,
        'value' => 2,
      ],
    ],
    'values' => [
      13 => [
        'name' => clienttranslate('[13] Omaha Beach Overlord'),
        'tmdisplay' => '[13]',
        'description' => clienttranslate('Omaha Beach Overlord'),
        'premium' => true,
      ],
      1367 => [
        'name' => clienttranslate('[1367] Prokhorovka Overlord'),
        'tmdisplay' => '[1367]',
        'description' => clienttranslate('Prokhorovka Overlord'),
        'premium' => true,
      ],

      1517 => [
        'name' => clienttranslate('[1517] Peleliu Landings Overlord'),
        'tmdisplay' => '[1517]',
        'description' => clienttranslate('Peleliu Landings Overlord'),
        'premium' => true,
      ],
    ],
  ],

  107 => [
    'name' => \clienttranslate('Scenario source'),
    'displaycondition' => [
      0 => [
        'type' => 'otheroption',
        'id' => 102,
        'value' => 2,
      ],
    ],
    'values' => [
      0 => [
        'name' => clienttranslate('Days of Wonders database'),
        'description' => clienttranslate('Days of Wonders database'),
      ],
      1 => [
        'name' => clienttranslate('M44 file'),
        'tmdisplay' => '[m44]',
        'description' => clienttranslate('Uploading a m44 file made with the editor'),
      ],
    ],
  ],

  106 => [
    'name' => clienttranslate('Side of the first player at the table'),
    'displaycondition' => [
      0 => [
        'type' => 'otheroption',
        'id' => 100,
        'value' => 2,
      ],
    ],
    'values' => [
      0 => [
        'name' => clienttranslate('Random'),
        'description' => clienttranslate('Random'),
      ],
      1 => [
        'name' => clienttranslate('Allies'),
        'tmdisplay' => \clienttranslate('[Allies]'),
      ],
      2 => [
        'name' => clienttranslate('Axis'),
        'tmdisplay' => \clienttranslate('[Axis]'),
      ],
    ],
  ],
];

$game_preferences = [
  103 => [
    'name' => totranslate('Turn confirmation'),
    'needReload' => false,
    'values' => [
      1 => ['name' => totranslate('Enabled with timer')],
      2 => ['name' => totranslate('Enabled')],
      0 => ['name' => totranslate('Disabled')],
    ],
  ],
];
