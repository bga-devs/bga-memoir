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
      ],
      3 => [
        'name' => clienttranslate('Overlord'),
        'tmpdisplay' => clienttranslate('Overlord'),
        'description' => clienttranslate('Overlord scenario'),
      ],
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
      2 => [
        'name' => clienttranslate('[2] Pegasus Bridge'),
        'tmdisplay' => '[2]',
        'description' => clienttranslate('Pegasus Bridge'),
      ],
      3 => [
        'name' => clienttranslate('[3] Sainte-Mère-Eglise'),
        'tmdisplay' => '[3]',
        'description' => clienttranslate('Sainte-Mère-Eglise'),
      ],
      4 => [
        'name' => clienttranslate('[4] Sword Beach'),
        'tmdisplay' => '[4]',
        'description' => clienttranslate('Sword Beach'),
      ],
      5 => [
        'name' => clienttranslate('[5] Mont Mouchet'),
        'tmdisplay' => '[5]',
        'description' => clienttranslate('Mont Mouchet'),
      ],
      7 => [
        'name' => clienttranslate('[7] Pointe-du-Hoc'),
        'tmdisplay' => '[7]',
        'description' => clienttranslate('Pointe-du-Hoc'),
      ],
      11 => [
        'name' => clienttranslate('[11] First Assault Wave'),
        'tmdisplay' => '[11]',
        'description' => clienttranslate('First Assault Wave'),
      ],
      15 => [
        'name' => clienttranslate('[15] Operation Cobra'),
        'tmdisplay' => '[15]',
        'description' => clienttranslate('Operation Cobra'),
      ],
      16 => [
        'name' => clienttranslate('[16] Counter-attack on Mortain'),
        'tmdisplay' => '[16]',
        'description' => clienttranslate('Counter-attack on Mortain'),
      ],
      18 => [
        'name' => clienttranslate('[18] Montélimar'),
        'tmdisplay' => '[18]',
        'description' => clienttranslate('Montélimar'),
      ],
      19 => [
        'name' => clienttranslate('[19] Vassieux'),
        'tmdisplay' => '[19]',
        'description' => clienttranslate('Vassieux'),
      ],
      20 => [
        'name' => clienttranslate('[20] St Vith'),
        'tmdisplay' => '[20]',
        'description' => clienttranslate('St Vith'),
      ],
      22 => [
        'name' => clienttranslate('[22] Liberation of Paris'),
        'tmdisplay' => '[22]',
        'description' => clienttranslate('Liberation of Paris'),
      ],
      23 => [
        'name' => clienttranslate('[23] Toulon'),
        'tmdisplay' => '[23]',
        'description' => clienttranslate('Toulon'),
      ],
      24 => [
        'name' => clienttranslate('[24] Arracourt'),
        'tmdisplay' => '[24]',
        'description' => clienttranslate('Arracourt'),
      ],
      25 => [
        'name' => clienttranslate('[25] Arnhem Bridge'),
        'tmdisplay' => '[25]',
        'description' => clienttranslate('Arnhem Bridge'),
      ],
      29 => [
        'name' => clienttranslate('[29] Saverne Gap'),
        'tmdisplay' => '[29]',
        'description' => clienttranslate('Saverne Gap'),
      ],
      1302 => [
        'name' => clienttranslate('[1302] Nijmegen Bridges'),
        'tmdisplay' => '[1302]',
        'description' => clienttranslate('Nijmegen Bridges'),
      ],
      1311 => [
        'name' => clienttranslate('[1311] Bug River'),
        'tmdisplay' => '[1311]',
        'description' => clienttranslate('Bug River'),
      ],
      1325 => [
        'name' => clienttranslate('[1325] Gates of Moscow'),
        'tmdisplay' => '[1325]',
        'description' => clienttranslate('Gates of Moscow'),
      ],
      1340 => [
        'name' => clienttranslate('[1340] Across the River Roer'),
        'tmdisplay' => '[1340]',
        'description' => clienttranslate('Across the River Roer'),
      ],
      1346 => [
        'name' => clienttranslate('[1346] Knightsbridge'),
        'tmdisplay' => '[1346]',
        'description' => clienttranslate('Knightsbridge'),
      ],
      1365 => [
        'name' => clienttranslate('[1365] Ponyri'),
        'tmdisplay' => '[1365]',
        'description' => clienttranslate('Ponyri'),
      ],
      1407 => [
        'name' => clienttranslate('[1407] Red Barricades Factory'),
        'tmdisplay' => '[1407]',
        'description' => clienttranslate('Red Barricades Factory'),
      ],
      1409 => [
        'name' => clienttranslate('[1409] Suomussalmi'),
        'tmdisplay' => '[1409]',
        'description' => clienttranslate('Suomussalmi'),
      ],
      1422 => [
        'name' => clienttranslate('[1422] Schwammenauel Dam'),
        'tmdisplay' => '[1422]',
        'description' => clienttranslate('Schwammenauel Dam'),
      ],
      1433 => [
        'name' => clienttranslate('[1433] Breakout at Klin'),
        'tmdisplay' => '[1433]',
        'description' => clienttranslate('Breakout at Klin'),
      ],
      1434 => [
        'name' => clienttranslate('[1434] Breakout to Lisyanka'),
        'tmdisplay' => '[1434]',
        'description' => clienttranslate('Breakout to Lisyanka'),
      ],
      23806 => [
        'name' => clienttranslate('[23806] [Battle of the Bulge] German counter-attack at Chaumont [FFM44]'),
        'tmdisplay' => '[23806]',
        'description' => clienttranslate('[Battle of the Bulge] German counter-attack at Chaumont [FFM44]'),
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
      18261 => [
        'name' => clienttranslate('[18261] [Ukraine] Soviet raid on Grigorevka'),
        'tmdisplay' => '[18261]',
        'description' => clienttranslate('[Ukraine] Soviet raid on Grigorevka'),
      ],
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
      ],
      1367 => [
        'name' => clienttranslate('[1367] Prokhorovka Overlord'),
        'tmdisplay' => '[1367]',
        'description' => clienttranslate('Prokhorovka Overlord'),
      ],
    ],
  ],
];

$game_preferences = [];
