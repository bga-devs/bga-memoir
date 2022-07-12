<?php
namespace M44\Scenarios;

$scenarios[6370] = [
  'meta_data' => [
    'scenario_id' => '6370',
    'status' => 'PRIVATE',
    'software' => 'sed 1.2',
  ],
  'game_info' => [
    'date_begin' => '1942-08-30',
    'front' => 'MEDITERRANEAN',
    'type' => 'HISTORICAL',
    'starting' => 'PLAYER2',
    'side_player1' => 'AXIS',
    'side_player2' => 'ALLIES',
    'country_player1' => 'DE',
    'country_player2' => 'US',
    'cards_player1' => 6,
    'cards_player2' => 6,
    'victory_player1' => 8,
    'victory_player2' => 8,
    'date_end' => '1942-09-07',
    'options' => [
      'north_african_desert_rules' => true,
      'british_commonwealth' => true,
    ],
  ],
  'board' => [
    'type' => 'BRKTHRU',
    'face' => 'DESERT',
    'hexagons' => [
      0 => [
        'row' => 0,
        'col' => 16,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      1 => [
        'row' => 1,
        'col' => 5,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      2 => [
        'row' => 1,
        'col' => 7,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      3 => [
        'row' => 1,
        'col' => 13,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      4 => [
        'row' => 1,
        'col' => 15,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      5 => [
        'row' => 2,
        'col' => 2,
        'unit' => [
          'name' => 'infger',
          'badge' => 'badge6',
        ],
      ],
      6 => [
        'row' => 2,
        'col' => 4,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      7 => [
        'row' => 2,
        'col' => 6,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      8 => [
        'row' => 2,
        'col' => 8,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      9 => [
        'row' => 2,
        'col' => 10,
        'unit' => [
          'name' => 'infger',
          'badge' => 'badge6',
        ],
      ],
      10 => [
        'row' => 2,
        'col' => 12,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      11 => [
        'row' => 2,
        'col' => 14,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      12 => [
        'row' => 2,
        'col' => 16,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      13 => [
        'row' => 2,
        'col' => 18,
        'unit' => [
          'name' => 'gunger',
        ],
      ],
      14 => [
        'row' => 3,
        'col' => 1,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      15 => [
        'row' => 3,
        'col' => 3,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      16 => [
        'row' => 3,
        'col' => 19,
        'unit' => [
          'name' => 'infger',
          'badge' => 'badge6',
        ],
      ],
      17 => [
        'row' => 4,
        'col' => 0,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      18 => [
        'row' => 4,
        'col' => 18,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      19 => [
        'row' => 4,
        'col' => 20,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      20 => [
        'row' => 4,
        'col' => 22,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      21 => [
        'row' => 5,
        'col' => 1,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      22 => [
        'row' => 5,
        'col' => 3,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      23 => [
        'row' => 5,
        'col' => 13,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      24 => [
        'row' => 5,
        'col' => 15,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      25 => [
        'row' => 5,
        'col' => 17,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      26 => [
        'row' => 5,
        'col' => 19,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      27 => [
        'row' => 6,
        'col' => 12,
        'terrain' => [
          'name' => 'dhill',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      28 => [
        'row' => 6,
        'col' => 14,
        'terrain' => [
          'name' => 'dhill',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      29 => [
        'row' => 6,
        'col' => 18,
        'terrain' => [
          'name' => 'dhill',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      30 => [
        'row' => 6,
        'col' => 20,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      31 => [
        'row' => 6,
        'col' => 24,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      32 => [
        'row' => 7,
        'col' => 3,
        'terrain' => [
          'name' => 'dridge',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'gunbrit',
        ],
      ],
      33 => [
        'row' => 7,
        'col' => 5,
        'terrain' => [
          'name' => 'dridge',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      34 => [
        'row' => 7,
        'col' => 7,
        'terrain' => [
          'name' => 'dridge',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      35 => [
        'row' => 7,
        'col' => 19,
        'terrain' => [
          'name' => 'dhill',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      36 => [
        'row' => 7,
        'col' => 21,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      37 => [
        'row' => 7,
        'col' => 23,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      38 => [
        'row' => 8,
        'col' => 0,
        'terrain' => [
          'name' => 'dridge',
        ],
      ],
      39 => [
        'row' => 8,
        'col' => 2,
        'terrain' => [
          'name' => 'dridge',
        ],
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      40 => [
        'row' => 8,
        'col' => 20,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      41 => [
        'row' => 8,
        'col' => 22,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      42 => [
        'row' => 9,
        'col' => 15,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      43 => [
        'row' => 9,
        'col' => 17,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      44 => [
        'row' => 9,
        'col' => 19,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      45 => [
        'row' => 9,
        'col' => 23,
        'unit' => [
          'name' => 'gunger',
        ],
      ],
      46 => [
        'row' => 10,
        'col' => 0,
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      47 => [
        'row' => 10,
        'col' => 2,
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      48 => [
        'row' => 10,
        'col' => 4,
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      49 => [
        'row' => 10,
        'col' => 10,
        'terrain' => [
          'name' => 'dridge',
        ],
      ],
      50 => [
        'row' => 10,
        'col' => 12,
        'terrain' => [
          'name' => 'dridge',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      51 => [
        'row' => 10,
        'col' => 14,
        'terrain' => [
          'name' => 'dridge',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      52 => [
        'row' => 10,
        'col' => 16,
        'terrain' => [
          'name' => 'dridge',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'gunbrit',
        ],
      ],
      53 => [
        'row' => 10,
        'col' => 20,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      54 => [
        'row' => 10,
        'col' => 24,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      55 => [
        'row' => 11,
        'col' => 5,
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      56 => [
        'row' => 11,
        'col' => 11,
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      57 => [
        'row' => 11,
        'col' => 21,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      58 => [
        'row' => 12,
        'col' => 18,
        'terrain' => [
          'name' => 'dhill',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      59 => [
        'row' => 12,
        'col' => 22,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      60 => [
        'row' => 12,
        'col' => 24,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      61 => [
        'row' => 13,
        'col' => 17,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      62 => [
        'row' => 13,
        'col' => 23,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      63 => [
        'row' => 14,
        'col' => 2,
        'terrain' => [
          'name' => 'bled',
        ],
        'unit' => [
          'name' => 'infbrit',
        ],
        'tags' => [
          0 => [
            'name' => 'medal2',
            'medal' => [
              'permanent' => true,
            ]
          ],
        ],
      ],
      64 => [
        'row' => 14,
        'col' => 12,
        'terrain' => [
          'name' => 'bled',
        ],
        'unit' => [
          'name' => 'infbrit',
        ],
        'tags' => [
          0 => [
            'name' => 'medal2',
            'medal' => [
              'permanent' => true,
            ]
          ],
        ],
      ],
      65 => [
        'row' => 14,
        'col' => 20,
        'terrain' => [
          'name' => 'dhill',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      66 => [
        'row' => 15,
        'col' => 1,
        'terrain' => [
          'name' => 'rail',
          'orientation' => 1,
        ],
      ],
      67 => [
        'row' => 15,
        'col' => 3,
        'terrain' => [
          'name' => 'rail',
          'orientation' => 1,
        ],
      ],
      68 => [
        'row' => 15,
        'col' => 5,
        'terrain' => [
          'name' => 'rail',
          'orientation' => 1,
        ],
      ],
      69 => [
        'row' => 15,
        'col' => 7,
        'terrain' => [
          'name' => 'rail',
          'orientation' => 1,
        ],
      ],
      70 => [
        'row' => 15,
        'col' => 9,
        'terrain' => [
          'name' => 'rail',
          'orientation' => 1,
        ],
      ],
      71 => [
        'row' => 15,
        'col' => 11,
        'terrain' => [
          'name' => 'rail',
          'orientation' => 1,
        ],
      ],
      72 => [
        'row' => 15,
        'col' => 13,
        'terrain' => [
          'name' => 'rail',
          'orientation' => 1,
        ],
      ],
      73 => [
        'row' => 15,
        'col' => 15,
        'terrain' => [
          'name' => 'railcurve',
          'orientation' => 5,
        ],
      ],
      74 => [
        'row' => 15,
        'col' => 17,
        'terrain' => [
          'name' => 'bled',
        ],
        'tags' => [
          0 => [
            'name' => 'medal2',
            'medal' => [
              'permanent' => true,
            ]
          ],
        ],
      ],
      75 => [
        'row' => 16,
        'col' => 0,
        'terrain' => [
          'name' => 'coast',
          'orientation' => 1,
        ],
      ],
      76 => [
        'row' => 16,
        'col' => 2,
        'terrain' => [
          'name' => 'coast',
          'orientation' => 1,
        ],
      ],
      77 => [
        'row' => 16,
        'col' => 4,
        'terrain' => [
          'name' => 'coast',
          'orientation' => 1,
        ],
      ],
      78 => [
        'row' => 16,
        'col' => 6,
        'terrain' => [
          'name' => 'coast',
          'orientation' => 1,
        ],
      ],
      79 => [
        'row' => 16,
        'col' => 8,
        'terrain' => [
          'name' => 'coast',
          'orientation' => 1,
        ],
      ],
      80 => [
        'row' => 16,
        'col' => 10,
        'terrain' => [
          'name' => 'coast',
          'orientation' => 1,
        ],
      ],
      81 => [
        'row' => 16,
        'col' => 12,
        'terrain' => [
          'name' => 'coast',
          'orientation' => 1,
        ],
      ],
      82 => [
        'row' => 16,
        'col' => 14,
        'terrain' => [
          'name' => 'coastcurve',
          'orientation' => 5,
        ],
      ],
      83 => [
        'row' => 16,
        'col' => 16,
        'terrain' => [
          'name' => 'rail',
          'orientation' => 3,
        ],
      ],
    ],
    'labels' => [
      0 => [
        'row' => 8,
        'col' => 6,
        'text' => [
          0 => clienttranslate('Alam El Halfa Ridge'),
        ],
      ],
      1 => [
        'row' => 11,
        'col' => 15,
        'text' => [
          0 => clienttranslate('Ruweisat Ridge'),
        ],
      ],
      2 => [
        'row' => 13,
        'col' => 13,
        'text' => [
          0 => clienttranslate('El Alamein'),
        ],
      ],
    ],
  ],
  'text' => [
    'en' => [
      'name' => clienttranslate('Battle of Alam el Halfa'),
      'subtitle' => clienttranslate('			'),
      'description' => clienttranslate('Axis Player [Germany]
Take 6 Command cards.

Allied Player [Great Britain]
Take 6 Command cards.
You move first.'),
      'rules' => clienttranslate('North African Desert rules are in effect (Actions 9 - North African Desert Rules).

British Commonwealth Forces command rules (Nation 5 - British Commonwealth Forces) are in effect.

Place a badge on the German engineer units (Troops 4 - Combat Engineers).

The Allied player lays out the Minefields (Terrain 29 -Minefields).

Air Rules are not in effect. The Air Sortie cards are set aside and not used in this scenario.'),
      'historical' => clienttranslate('The battle of Alam el Halfa might be Rommel\'s last shot at defeating the British Eighth Army and taking Egypt; his Afrika Corps numerical superiority was slowing dwindling away and would soon be outnumbered by the Allies. With waiting no longer an option, he set in motion the same maneuver he had attempted at Gazala.

Informed by ULTRA of the Axis plan to outflank his front lines and attack from the south, Montgomery carefully positioned his forces along the Alam el Halfa and Ruweisat ridges. Digging in some of his tanks and willing to stay on the defensive, Montgomery was finally about to test his military doctrine in his first battle in North Africa.

The stage is set, the battle lines are drawn, and you are in command. The rest is history.'),
      'victory' => clienttranslate('8 Medals.

The three town hexes next to the railroad tracks are Permanent Medal Objectives for the Axis forces.'),
    ],
  ],
];
