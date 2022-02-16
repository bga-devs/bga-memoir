<?php
namespace M44\Scenarios;

$scenarios[29] = [
  'meta_data' => [
    'scenario_id' => '29',
    'status' => 'GAME',
    'can_translate' => 'false',
    'mod_date' => '2010-05-27 06:35:44',
    'ownerID' => 8860,
    'software' => 'sed 1.2',
    'create_by' => 8860,
    'create_date' => '2008-11-14 11:52:33',
    'mod_by' => 291520,
    'author_id' => 8860,
  ],
  'game_info' => [
    'front' => 'WESTERN',
    'date_begin' => '1944-11-19',
    'date_end' => '1944-11-23',
    'type' => 'HISTORICAL',
    'starting' => 'PLAYER2',
    'side_player1' => 'AXIS',
    'side_player2' => 'ALLIES',
    'country_player1' => 'DE',
    'country_player2' => 'FR',
    'cards_player1' => 4,
    'cards_player2' => 6,
    'victory_player1' => 5,
    'victory_player2' => 5,
    'victory_conditions' => [
      0 => [
        'standard' => [],
      ],
    ],
    'options' => [],
    'victory' => [
      'condition' => [
        0 => [
          'group_sudden_death' => [
            'side' => 'ALLIES',
            'number' => 2,
            'group' => [
              0 => 'g8',
              1 => 'G9',
              2 => 'F9',
            ],
          ],
        ],
      ],
    ],
  ],
  'board' => [
    'type' => 'STANDARD',
    'face' => 'COUNTRY',
    'hexagons' => [
      0 => [
        'row' => 0,
        'col' => 6,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      1 => [
        'row' => 0,
        'col' => 10,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      2 => [
        'row' => 0,
        'col' => 12,
        'terrain' => [
          'name' => 'buildings',
        ],
      ],
      3 => [
        'row' => 0,
        'col' => 16,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      4 => [
        'row' => 0,
        'col' => 18,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
      ],
      5 => [
        'row' => 1,
        'col' => 3,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      6 => [
        'row' => 1,
        'col' => 7,
        'terrain' => [
          'name' => 'hills',
          'behavior' => 'IMPASSABLE_BLOCKING_HILL',
        ],
      ],
      7 => [
        'row' => 1,
        'col' => 13,
        'terrain' => [
          'name' => 'buildings',
        ],
      ],
      8 => [
        'row' => 1,
        'col' => 17,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 1,
        ],
        'rect_terrain' => [
          'name' => 'bridge',
          'orientation' => 3,
        ],
      ],
      9 => [
        'row' => 1,
        'col' => 21,
        'terrain' => [
          'name' => 'woods',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      10 => [
        'row' => 2,
        'col' => 2,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      11 => [
        'row' => 2,
        'col' => 8,
        'terrain' => [
          'name' => 'hills',
          'behavior' => 'IMPASSABLE_BLOCKING_HILL',
        ],
      ],
      12 => [
        'row' => 2,
        'col' => 10,
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'gunger',
        ],
      ],
      13 => [
        'row' => 2,
        'col' => 18,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 4,
        ],
      ],
      14 => [
        'row' => 2,
        'col' => 24,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      15 => [
        'row' => 3,
        'col' => 5,
        'terrain' => [
          'name' => 'hills',
          'behavior' => 'IMPASSABLE_BLOCKING_HILL',
        ],
      ],
      16 => [
        'row' => 3,
        'col' => 7,
        'terrain' => [
          'name' => 'hills',
          'behavior' => 'IMPASSABLE_BLOCKING_HILL',
        ],
      ],
      17 => [
        'row' => 3,
        'col' => 13,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      18 => [
        'row' => 3,
        'col' => 15,
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      19 => [
        'row' => 3,
        'col' => 17,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
      ],
      20 => [
        'row' => 3,
        'col' => 19,
        'terrain' => [
          'name' => 'hills',
          'behavior' => 'IMPASSABLE_BLOCKING_HILL',
        ],
      ],
      21 => [
        'row' => 3,
        'col' => 21,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      22 => [
        'row' => 4,
        'col' => 0,
        'terrain' => [
          'name' => 'hills',
          'behavior' => 'IMPASSABLE_BLOCKING_HILL',
        ],
      ],
      23 => [
        'row' => 4,
        'col' => 8,
        'terrain' => [
          'name' => 'hills',
          'behavior' => 'IMPASSABLE_BLOCKING_HILL',
        ],
      ],
      24 => [
        'row' => 4,
        'col' => 16,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 1,
        ],
      ],
      25 => [
        'row' => 4,
        'col' => 18,
        'terrain' => [
          'name' => 'hills',
          'behavior' => 'IMPASSABLE_BLOCKING_HILL',
        ],
      ],
      26 => [
        'row' => 4,
        'col' => 20,
        'terrain' => [
          'name' => 'hills',
          'behavior' => 'IMPASSABLE_BLOCKING_HILL',
        ],
      ],
      27 => [
        'row' => 4,
        'col' => 24,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      28 => [
        'row' => 5,
        'col' => 1,
        'terrain' => [
          'name' => 'hills',
          'behavior' => 'IMPASSABLE_BLOCKING_HILL',
        ],
      ],
      29 => [
        'row' => 5,
        'col' => 5,
        'terrain' => [
          'name' => 'hills',
          'behavior' => 'IMPASSABLE_BLOCKING_HILL',
        ],
      ],
      30 => [
        'row' => 5,
        'col' => 7,
        'terrain' => [
          'name' => 'hills',
          'behavior' => 'IMPASSABLE_BLOCKING_HILL',
        ],
      ],
      31 => [
        'row' => 5,
        'col' => 11,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      32 => [
        'row' => 5,
        'col' => 17,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 4,
        ],
      ],
      33 => [
        'row' => 5,
        'col' => 19,
        'terrain' => [
          'name' => 'hills',
          'behavior' => 'IMPASSABLE_BLOCKING_HILL',
        ],
      ],
      34 => [
        'row' => 5,
        'col' => 23,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      35 => [
        'row' => 6,
        'col' => 2,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      36 => [
        'row' => 6,
        'col' => 6,
        'terrain' => [
          'name' => 'hills',
          'behavior' => 'IMPASSABLE_BLOCKING_HILL',
        ],
      ],
      37 => [
        'row' => 6,
        'col' => 8,
        'terrain' => [
          'name' => 'hills',
          'behavior' => 'IMPASSABLE_BLOCKING_HILL',
        ],
      ],
      38 => [
        'row' => 6,
        'col' => 10,
        'obstacle' => [
          'name' => 'wire',
          'orientation' => 1,
        ],
      ],
      39 => [
        'row' => 6,
        'col' => 12,
        'obstacle' => [
          'name' => 'wire',
          'orientation' => 1,
        ],
      ],
      40 => [
        'row' => 6,
        'col' => 14,
        'obstacle' => [
          'name' => 'wire',
          'orientation' => 1,
        ],
      ],
      41 => [
        'row' => 6,
        'col' => 16,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
      ],
      42 => [
        'row' => 6,
        'col' => 18,
        'terrain' => [
          'name' => 'hills',
          'behavior' => 'IMPASSABLE_BLOCKING_HILL',
        ],
      ],
      43 => [
        'row' => 6,
        'col' => 22,
        'terrain' => [
          'name' => 'hills',
          'behavior' => 'IMPASSABLE_BLOCKING_HILL',
        ],
      ],
      44 => [
        'row' => 7,
        'col' => 1,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      45 => [
        'row' => 7,
        'col' => 3,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      46 => [
        'row' => 7,
        'col' => 5,
        'terrain' => [
          'name' => 'hills',
          'behavior' => 'IMPASSABLE_BLOCKING_HILL',
        ],
      ],
      47 => [
        'row' => 7,
        'col' => 7,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      48 => [
        'row' => 7,
        'col' => 15,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 1,
        ],
      ],
      49 => [
        'row' => 7,
        'col' => 17,
        'terrain' => [
          'name' => 'hills',
          'behavior' => 'IMPASSABLE_BLOCKING_HILL',
        ],
      ],
      50 => [
        'row' => 7,
        'col' => 19,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      51 => [
        'row' => 7,
        'col' => 23,
        'terrain' => [
          'name' => 'hills',
          'behavior' => 'IMPASSABLE_BLOCKING_HILL',
        ],
      ],
      52 => [
        'row' => 8,
        'col' => 4,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      53 => [
        'row' => 8,
        'col' => 6,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      54 => [
        'row' => 8,
        'col' => 8,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      55 => [
        'row' => 8,
        'col' => 10,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      56 => [
        'row' => 8,
        'col' => 12,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      57 => [
        'row' => 8,
        'col' => 14,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      58 => [
        'row' => 8,
        'col' => 16,
        'terrain' => [
          'name' => 'river',
          'orientation' => 3,
        ],
      ],
      59 => [
        'row' => 8,
        'col' => 18,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      60 => [
        'row' => 8,
        'col' => 20,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      61 => [
        'row' => 8,
        'col' => 22,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      62 => [
        'row' => 8,
        'col' => 24,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
    ],
    'labels' => [
      0 => [
        'row' => 0,
        'col' => 14,
        'text' => [
          0 => clienttranslate('Saverne'),
        ],
      ],
      1 => [
        'row' => 0,
        'col' => 18,
        'text' => [
          0 => clienttranslate('Rhine-Marne Canal'),
        ],
      ],
      2 => [
        'row' => 1,
        'col' => 1,
        'text' => [
          0 => clienttranslate('La Petite-Pierre'),
        ],
      ],
      3 => [
        'row' => 3,
        'col' => 11,
        'text' => [
          0 => clienttranslate('Saverne Gap'),
        ],
      ],
      4 => [
        'row' => 4,
        'col' => 22,
        'text' => [
          0 => clienttranslate('Dabo'),
        ],
      ],
      5 => [
        'row' => 5,
        'col' => 13,
        'text' => [
          0 => clienttranslate('Phalsbourg'),
        ],
      ],
    ],
  ],
  'text' => [
    'en' => [
      'name' => clienttranslate('Saverne Gap'),
      'subtitle' => clienttranslate('Vosges'),
      'historical' => clienttranslate('The Saverne Gap, cutting through the Vosges mountains, was the key to Strasbourg, capital city of Alsace. On November 21st, the US Seventh Army XV Corps, under the command of General Wade Hampton Halslip, arrived to the front lines at Phalsbourg.

With the infantry progressing forward into the Saverne Gap, General Leclerc divided his 2nd French Armor division into two task forces. The first would move well north of the Gap by La Petite-Pierre, the other on secondary roads in the south through heavily forested mountains by Dabo. If the plan worked, they would take Saverne simultaneously from both the north and south, avoiding the strong defenses expected in the Gap itself.

The plan worked to perfection. One of the south French armor group was even able to rush through Saverne\'s western end and climb to the Gap, taking the German defenses from behind. German forces, few in numbers, fought valiantly; but, without support or reserves, they were unable to stop the three-pronged Allied attack; they crumbled, leaving the door to Strasbourg wide open.

The stage is set, the battle lines are drawn, and you are in command. The rest is history.'),
      'description' => clienttranslate('Axis Player: Take 4 command cards.

Allied Player: Take 6 command cards.
You move first.'),
      'victory' => clienttranslate('5 Medals
If Allied units occupy 2 town hexes in Saverne at the end of their turn, they win immediately.'),
      'rules' => clienttranslate('The hills are impassable.
Artillery may not fire over hills.'),
    ],
  ],
];
