<?php
namespace M44\Scenarios;

$scenarios[6380] = [
  'meta_data' => [
    'scenario_id' => '6380',
    'status' => 'PRIVATE',
    'software' => 'sed 1.2',
  ],
  'game_info' => [
    'date_begin' => '1944-12-20',
    'front' => 'WESTERN',
    'type' => 'HISTORICAL',
    'starting' => 'PLAYER1',
    'side_player1' => 'AXIS',
    'side_player2' => 'ALLIES',
    'country_player1' => 'DE',
    'country_player2' => 'US',
    'cards_player1' => 4,
    'cards_player2' => 5,
    'victory_player1' => 10,
    'victory_player2' => 10,
  ],
  'board' => [
    'type' => 'BRKTHRU',
    'face' => 'WINTER',
    'hexagons' => [
      0 => [
        'row' => 0,
        'col' => 10,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      1 => [
        'row' => 0,
        'col' => 14,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      2 => [
        'row' => 0,
        'col' => 16,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      3 => [
        'row' => 0,
        'col' => 18,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      4 => [
        'row' => 0,
        'col' => 20,
        'terrain' => [
          'name' => 'wroad',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      5 => [
        'row' => 0,
        'col' => 22,
        'unit' => [
          'name' => 'gunger',
        ],
      ],
      6 => [
        'row' => 1,
        'col' => 3,
        'unit' => [
          'name' => 'tank2ger',
          'nbr_units' => '1',
        ],
      ],
      7 => [
        'row' => 1,
        'col' => 5,
        'unit' => [
          'name' => 'gunger',
        ],
      ],
      8 => [
        'row' => 1,
        'col' => 11,
        'terrain' => [
          'name' => 'whill',
        ],
      ],
      9 => [
        'row' => 1,
        'col' => 13,
        'terrain' => [
          'name' => 'whill',
        ],
      ],
      10 => [
        'row' => 1,
        'col' => 19,
        'terrain' => [
          'name' => 'hillroad',
          'orientation' => 2,
        ],
      ],
      11 => [
        'row' => 1,
        'col' => 21,
        'terrain' => [
          'name' => 'whill',
        ],
      ],
      12 => [
        'row' => 1,
        'col' => 23,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      13 => [
        'row' => 2,
        'col' => 0,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      14 => [
        'row' => 2,
        'col' => 2,
        'unit' => [
          'name' => 'infger',
          'badge' => 'badge6',
        ],
      ],
      15 => [
        'row' => 2,
        'col' => 4,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      16 => [
        'row' => 2,
        'col' => 8,
        'terrain' => [
          'name' => 'whill',
        ],
      ],
      17 => [
        'row' => 2,
        'col' => 18,
        'terrain' => [
          'name' => 'wroad',
          'orientation' => 2,
        ],
      ],
      18 => [
        'row' => 2,
        'col' => 22,
        'terrain' => [
          'name' => 'whill',
        ],
      ],
      19 => [
        'row' => 3,
        'col' => 1,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      20 => [
        'row' => 3,
        'col' => 5,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      21 => [
        'row' => 3,
        'col' => 7,
        'terrain' => [
          'name' => 'whill',
        ],
      ],
      22 => [
        'row' => 3,
        'col' => 9,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      23 => [
        'row' => 3,
        'col' => 15,
        'terrain' => [
          'name' => 'wvillage',
        ],
      ],
      24 => [
        'row' => 3,
        'col' => 17,
        'terrain' => [
          'name' => 'wroad',
          'orientation' => 2,
        ],
      ],
      25 => [
        'row' => 3,
        'col' => 19,
        'terrain' => [
          'name' => 'wvillage',
        ],
      ],
      26 => [
        'row' => 4,
        'col' => 6,
        'terrain' => [
          'name' => 'wroadcurve',
          'orientation' => 6,
        ],
      ],
      27 => [
        'row' => 4,
        'col' => 8,
        'terrain' => [
          'name' => 'hillroad',
          'orientation' => 1,
        ],
      ],
      28 => [
        'row' => 4,
        'col' => 10,
        'terrain' => [
          'name' => 'wroad',
          'orientation' => 1,
        ],
      ],
      29 => [
        'row' => 4,
        'col' => 12,
        'terrain' => [
          'name' => 'wroad',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'vehus',
          'nbr_units' => '2',
        ],
      ],
      30 => [
        'row' => 4,
        'col' => 14,
        'terrain' => [
          'name' => 'wroad',
          'orientation' => 1,
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'inf2us',
          'badge' => 'badge9',
        ],
      ],
      31 => [
        'row' => 4,
        'col' => 16,
        'terrain' => [
          'name' => 'wroadFR',
          'orientation' => 5,
        ],
      ],
      32 => [
        'row' => 4,
        'col' => 18,
        'terrain' => [
          'name' => 'wvillage',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'infus',
          'badge' => 'badge37',
        ],
      ],
      33 => [
        'row' => 5,
        'col' => 5,
        'terrain' => [
          'name' => 'hillroad',
          'orientation' => 2,
        ],
      ],
      34 => [
        'row' => 5,
        'col' => 7,
        'terrain' => [
          'name' => 'whill',
        ],
      ],
      35 => [
        'row' => 5,
        'col' => 9,
        'terrain' => [
          'name' => 'wforest',
        ],
        'unit' => [
          'name' => 'infus',
        ],
      ],
      36 => [
        'row' => 5,
        'col' => 15,
        'terrain' => [
          'name' => 'wroad',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'vehus',
          'nbr_units' => '2',
        ],
      ],
      37 => [
        'row' => 5,
        'col' => 17,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      38 => [
        'row' => 5,
        'col' => 19,
        'terrain' => [
          'name' => 'wvillage',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'inf2us',
          'badge' => 'badge9',
        ],
      ],
      39 => [
        'row' => 6,
        'col' => 2,
        'terrain' => [
          'name' => 'wvillage',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'infus',
        ],
      ],
      40 => [
        'row' => 6,
        'col' => 4,
        'terrain' => [
          'name' => 'wroad',
          'orientation' => 2,
        ],
      ],
      41 => [
        'row' => 6,
        'col' => 14,
        'terrain' => [
          'name' => 'wroad',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      42 => [
        'row' => 6,
        'col' => 22,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      43 => [
        'row' => 6,
        'col' => 24,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      44 => [
        'row' => 7,
        'col' => 1,
        'terrain' => [
          'name' => 'wroadcurve',
          'orientation' => 6,
        ],
      ],
      45 => [
        'row' => 7,
        'col' => 3,
        'terrain' => [
          'name' => 'wroadY',
          'orientation' => 2,
        ],
      ],
      46 => [
        'row' => 7,
        'col' => 5,
        'terrain' => [
          'name' => 'wvillage',
        ],
        'unit' => [
          'name' => 'infus',
        ],
      ],
      47 => [
        'row' => 7,
        'col' => 13,
        'terrain' => [
          'name' => 'wroad',
          'orientation' => 2,
        ],
      ],
      48 => [
        'row' => 7,
        'col' => 21,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      49 => [
        'row' => 7,
        'col' => 23,
        'unit' => [
          'name' => 'tank2ger',
          'nbr_units' => '1',
        ],
      ],
      50 => [
        'row' => 8,
        'col' => 0,
        'terrain' => [
          'name' => 'wroadcurve',
          'orientation' => 3,
        ],
      ],
      51 => [
        'row' => 8,
        'col' => 4,
        'terrain' => [
          'name' => 'wroad',
          'orientation' => 3,
        ],
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      52 => [
        'row' => 8,
        'col' => 8,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      53 => [
        'row' => 8,
        'col' => 12,
        'terrain' => [
          'name' => 'wroad',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'vehus',
          'nbr_units' => '2',
        ],
      ],
      54 => [
        'row' => 8,
        'col' => 18,
        'terrain' => [
          'name' => 'wforest',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
          'badge' => 'badge37',
        ],
      ],
      55 => [
        'row' => 9,
        'col' => 5,
        'terrain' => [
          'name' => 'wroad',
          'orientation' => 3,
        ],
      ],
      56 => [
        'row' => 9,
        'col' => 11,
        'terrain' => [
          'name' => 'wroad',
          'orientation' => 2,
        ],
      ],
      57 => [
        'row' => 9,
        'col' => 17,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      58 => [
        'row' => 10,
        'col' => 2,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      59 => [
        'row' => 10,
        'col' => 4,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      60 => [
        'row' => 10,
        'col' => 6,
        'terrain' => [
          'name' => 'wroad',
          'orientation' => 3,
        ],
      ],
      61 => [
        'row' => 10,
        'col' => 10,
        'terrain' => [
          'name' => 'wroad',
          'orientation' => 2,
        ],
      ],
      62 => [
        'row' => 10,
        'col' => 16,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      63 => [
        'row' => 10,
        'col' => 20,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      64 => [
        'row' => 11,
        'col' => 7,
        'terrain' => [
          'name' => 'wroad',
          'orientation' => 3,
        ],
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      65 => [
        'row' => 11,
        'col' => 9,
        'terrain' => [
          'name' => 'wroad',
          'orientation' => 2,
        ],
      ],
      66 => [
        'row' => 11,
        'col' => 11,
        'terrain' => [
          'name' => 'wvillage',
        ],
        'unit' => [
          'name' => 'infus',
        ],
      ],
      67 => [
        'row' => 11,
        'col' => 19,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      68 => [
        'row' => 11,
        'col' => 21,
        'terrain' => [
          'name' => 'wrailcurve',
          'orientation' => 6,
        ],
      ],
      69 => [
        'row' => 11,
        'col' => 23,
        'terrain' => [
          'name' => 'wrail',
          'orientation' => 1,
        ],
      ],
      70 => [
        'row' => 12,
        'col' => 0,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      71 => [
        'row' => 12,
        'col' => 2,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      72 => [
        'row' => 12,
        'col' => 6,
        'terrain' => [
          'name' => 'wvillage',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'infus',
        ],
      ],
      73 => [
        'row' => 12,
        'col' => 8,
        'terrain' => [
          'name' => 'wroadFL',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      74 => [
        'row' => 12,
        'col' => 10,
        'terrain' => [
          'name' => 'wvillage',
        ],
      ],
      75 => [
        'row' => 12,
        'col' => 20,
        'terrain' => [
          'name' => 'wrail',
          'orientation' => 2,
        ],
      ],
      76 => [
        'row' => 13,
        'col' => 1,
        'unit' => [
          'name' => 'infger',
          'badge' => 'badge41',
        ],
      ],
      77 => [
        'row' => 13,
        'col' => 3,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      78 => [
        'row' => 13,
        'col' => 7,
        'terrain' => [
          'name' => 'wroad',
          'orientation' => 2,
        ],
      ],
      79 => [
        'row' => 13,
        'col' => 13,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      80 => [
        'row' => 13,
        'col' => 19,
        'terrain' => [
          'name' => 'wrail',
          'orientation' => 2,
        ],
      ],
      81 => [
        'row' => 14,
        'col' => 0,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      82 => [
        'row' => 14,
        'col' => 2,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      83 => [
        'row' => 14,
        'col' => 6,
        'terrain' => [
          'name' => 'wroad',
          'orientation' => 2,
        ],
      ],
      84 => [
        'row' => 14,
        'col' => 10,
        'terrain' => [
          'name' => 'whill',
        ],
      ],
      85 => [
        'row' => 14,
        'col' => 14,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      86 => [
        'row' => 14,
        'col' => 18,
        'terrain' => [
          'name' => 'wrail',
          'orientation' => 2,
        ],
      ],
      87 => [
        'row' => 14,
        'col' => 20,
        'unit' => [
          'name' => 'tank2ger',
          'nbr_units' => '1',
        ],
      ],
      88 => [
        'row' => 15,
        'col' => 5,
        'terrain' => [
          'name' => 'wroad',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'infus',
        ],
      ],
      89 => [
        'row' => 15,
        'col' => 7,
        'terrain' => [
          'name' => 'wforest',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'gunus',
        ],
      ],
      90 => [
        'row' => 15,
        'col' => 11,
        'terrain' => [
          'name' => 'whill',
        ],
      ],
      91 => [
        'row' => 15,
        'col' => 13,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      92 => [
        'row' => 15,
        'col' => 17,
        'terrain' => [
          'name' => 'wrail',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      93 => [
        'row' => 15,
        'col' => 19,
        'unit' => [
          'name' => 'gunger',
        ],
      ],
      94 => [
        'row' => 16,
        'col' => 4,
        'terrain' => [
          'name' => 'wroad',
          'orientation' => 2,
        ],
        'tags' => [
          0 => [
            'name' => 'tag4',
          ],
        ],
      ],
      95 => [
        'row' => 16,
        'col' => 16,
        'terrain' => [
          'name' => 'wrail',
          'orientation' => 2,
        ],
      ],
    ],
    'labels' => [
      0 => [
        'row' => 4,
        'col' => 6,
        'text' => [],
      ],
      1 => [
        'row' => 4,
        'col' => 16,
        'text' => [
          0 => clienttranslate('Noville'),
        ],
      ],
      2 => [
        'row' => 7,
        'col' => 3,
        'text' => [
          0 => clienttranslate('Recogne'),
        ],
      ],
      3 => [
        'row' => 12,
        'col' => 8,
        'text' => [
          0 => clienttranslate('Foy'),
        ],
      ],
      4 => [
        'row' => 16,
        'col' => 4,
        'text' => [
          0 => clienttranslate('Vers Bastogne'),
        ],
      ],
    ],
  ],
  'text' => [
    'en' => [
      'name' => clienttranslate('Noville to Foy'),
      'subtitle' => clienttranslate('Unternehmen Wacht am Rhein'),
      'description' => clienttranslate('Axis Player
[Germany]
Take 4 Command cards.
You move first.

Allied player
[United States]
Take 5 Command cards.'),
      'rules' => clienttranslate('?Heavy Fog? rules are in effect, reducing combat effectiveness. Dice symbols rolled that match a unit being targeted only score hits when battling the unit in Close Assault. All Grenades rolled still score hits as normal.

Place a badge on the two elite Allied Parachute infantry units (Troops 2 - Specialized Units) and on the lone German engineers unit (Troops 4 - Combat Engineers).

Tiger Tank rules are in effect (Troops 16 - Tiger Tanks).

Supply Truck rules are in effect (Troops 17 - Supply Trucks). Re-supply rules are not in effect, however; trucks in this scenario are transporting wounded men.

Special Weapon Assets rules (SWAs 1- Special Weapon Assets) are in effect for the units equipped with Anti-Tank weapons (SWAs 2 - Anti-Tank Gun) and Mortar (SWAs 3- Mortar).

Air Rules are not in effect. The Air Sortie cards are set aside and not used in this scenario.'),
      'historical' => clienttranslate('Battle of the Bulge, morning of December 20, 1944 - Noville is already under attack, but the enemy seems to be groping, rather than making a concentrated attack. Fog and snow have hampered the action and the beleaguered Americans can only tell what is happening from up real close. Allied Command has told them to withdraw to Foy, but with the fog, the sound of gunfire seems to swirl around them from every direction. Can the column make it to Foy, not to even mention Bastogne?

The stage is set, the battle lines are drawn, and you are in command. The rest is history.'),
      'victory' => clienttranslate('10 Medals.

The baseline road hex with an Exit marker is worth 1 Victory Medal for each Allied truck figure that manages to exit the board toward Bastogne.

The three sets of road hexes connecting Noville, Foy and Recogne are each worth a single Temporary Medal Objective for the Axis player. If the Axis player occupies one or more hexes in any of these sets at the start of his turn, he scores one Temporary Medal. Only one medal is gained for each set, regardless of the number of road hexes held there.'),
    ],
  ],
];
