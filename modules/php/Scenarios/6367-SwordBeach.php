<?php
namespace M44\Scenarios;

$scenarios[6367] = [
  'meta_data' => [
    'scenario_id' => '6367',
    'status' => 'PRIVATE',
    'software' => 'sed 1.2',
  ],
  'game_info' => [
    'date_begin' => '1944-06-06',
    'front' => 'WESTERN',
    'type' => 'HISTORICAL',
    'starting' => 'PLAYER2',
    'side_player1' => 'AXIS',
    'side_player2' => 'ALLIES',
    'country_player1' => 'DE',
    'country_player2' => 'GB',
    'cards_player1' => 5,
    'cards_player2' => 6,
    'victory_player1' => 12,
    'victory_player2' => 12,
  ],
  'board' => [
    'type' => 'BRKTHRU',
    'face' => 'BEACH',
    'hexagons' => [
      0 => [
        'row' => 0,
        'col' => 2,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
      ],
      1 => [
        'row' => 0,
        'col' => 6,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
      ],
      2 => [
        'row' => 0,
        'col' => 8,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      3 => [
        'row' => 0,
        'col' => 10,
        'terrain' => [
          'name' => 'buildings',
        ],
      ],
      4 => [
        'row' => 0,
        'col' => 12,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
        ],
        'tags' => [
          0 => [
            'name' => 'medal0',
            'medal' => [
              'counts_for' => 3,
              'majority' => true,
            ],
            'group' => [
              0 => 'F17',
            ],
          ],
        ],
      ],
      5 => [
        'row' => 0,
        'col' => 20,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      6 => [
        'row' => 0,
        'col' => 24,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      7 => [
        'row' => 1,
        'col' => 1,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
      ],
      8 => [
        'row' => 1,
        'col' => 3,
        'unit' => [
          'name' => 'inf2brit',
          'badge' => 'badge1',
        ],
      ],
      9 => [
        'row' => 1,
        'col' => 5,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
      ],
      10 => [
        'row' => 1,
        'col' => 7,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      11 => [
        'row' => 1,
        'col' => 21,
        'unit' => [
          'name' => 'inf2ger',
          'badge' => 'badge4',
        ],
      ],
      12 => [
        'row' => 1,
        'col' => 23,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      13 => [
        'row' => 2,
        'col' => 0,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
        'rect_terrain' => [
          'name' => 'bridge',
          'orientation' => 6,
        ],
        'tags' => [
          0 => [
            'name' => 'medal0',
            'medal' => [
              'counts_for' => 2,
              'majority' => true,
            ],
            'group' => [
              0 => 'B13',
            ],
          ],
        ],
      ],
      14 => [
        'row' => 2,
        'col' => 2,
        'terrain' => [
          'name' => 'hills',
        ],
        'unit' => [
          'name' => 'inf2brit',
          'badge' => 'badge1',
        ],
      ],
      15 => [
        'row' => 2,
        'col' => 4,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
      ],
      16 => [
        'row' => 2,
        'col' => 6,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      17 => [
        'row' => 2,
        'col' => 8,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      18 => [
        'row' => 2,
        'col' => 10,
        'terrain' => [
          'name' => 'hills',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      19 => [
        'row' => 2,
        'col' => 14,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      20 => [
        'row' => 2,
        'col' => 16,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      21 => [
        'row' => 3,
        'col' => 3,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
      ],
      22 => [
        'row' => 3,
        'col' => 19,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      23 => [
        'row' => 3,
        'col' => 21,
        'terrain' => [
          'name' => 'hedgerow',
        ],
      ],
      24 => [
        'row' => 4,
        'col' => 0,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      25 => [
        'row' => 4,
        'col' => 2,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
        'rect_terrain' => [
          'name' => 'bridge',
          'orientation' => 1,
        ],
      ],
      26 => [
        'row' => 4,
        'col' => 4,
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      27 => [
        'row' => 4,
        'col' => 14,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      28 => [
        'row' => 4,
        'col' => 20,
        'terrain' => [
          'name' => 'hedgerow',
        ],
      ],
      29 => [
        'row' => 4,
        'col' => 22,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      30 => [
        'row' => 5,
        'col' => 1,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
      ],
      31 => [
        'row' => 5,
        'col' => 9,
        'terrain' => [
          'name' => 'hills',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      32 => [
        'row' => 5,
        'col' => 11,
        'terrain' => [
          'name' => 'hills',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'gunger',
        ],
      ],
      33 => [
        'row' => 5,
        'col' => 13,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      34 => [
        'row' => 5,
        'col' => 21,
        'terrain' => [
          'name' => 'hedgerow',
        ],
      ],
      35 => [
        'row' => 6,
        'col' => 0,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
      ],
      36 => [
        'row' => 6,
        'col' => 2,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      37 => [
        'row' => 6,
        'col' => 18,
        'terrain' => [
          'name' => 'buildings',
        ],
      ],
      38 => [
        'row' => 7,
        'col' => 9,
        'terrain' => [
          'name' => 'hedgerow',
        ],
      ],
      39 => [
        'row' => 7,
        'col' => 19,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      40 => [
        'row' => 8,
        'col' => 0,
        'terrain' => [
          'name' => 'buildings',
        ],
      ],
      41 => [
        'row' => 8,
        'col' => 10,
        'terrain' => [
          'name' => 'buildings',
        ],
      ],
      42 => [
        'row' => 8,
        'col' => 14,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      43 => [
        'row' => 9,
        'col' => 7,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      44 => [
        'row' => 9,
        'col' => 17,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      45 => [
        'row' => 9,
        'col' => 19,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      46 => [
        'row' => 10,
        'col' => 2,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
        ],
        'tags' => [
          0 => [
            'name' => 'medal1',
            'medal' => [
              'permanent' => true,
            ],
          ],
        ],
      ],
      47 => [
        'row' => 10,
        'col' => 22,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
        ],
        'tags' => [
          0 => [
            'name' => 'medal1',
            'medal' => [
              'permanent' => true,
            ],
          ],
        ],
      ],
      48 => [
        'row' => 11,
        'col' => 5,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      49 => [
        'row' => 11,
        'col' => 7,
        'terrain' => [
          'name' => 'hills',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      50 => [
        'row' => 11,
        'col' => 9,
        'obstacle' => [
          'name' => 'wire',
          'orientation' => 1,
        ],
      ],
      51 => [
        'row' => 11,
        'col' => 11,
        'rect_terrain' => [
          'name' => 'bunker',
          'orientation' => 1,
          'original_owner' => 'AXIS',
        ],
        'unit' => [
          'name' => 'gunger',
        ],
      ],
      52 => [
        'row' => 11,
        'col' => 17,
        'obstacle' => [
          'name' => 'wire',
          'orientation' => 1,
        ],
      ],
      53 => [
        'row' => 11,
        'col' => 19,
        'rect_terrain' => [
          'name' => 'bunker',
          'orientation' => 1,
          'original_owner' => 'AXIS',
        ],
        'tags' => [
          0 => [
            'name' => 'medal1',
            'medal' => [
              'permanent' => true,
            ],
          ],
        ],
      ],
      54 => [
        'row' => 12,
        'col' => 2,
        'obstacle' => [
          'name' => 'hedgehog',
        ],
      ],
      55 => [
        'row' => 12,
        'col' => 4,
        'obstacle' => [
          'name' => 'wire',
          'orientation' => 1,
        ],
      ],
      56 => [
        'row' => 12,
        'col' => 6,
        'obstacle' => [
          'name' => 'hedgehog',
        ],
      ],
      57 => [
        'row' => 12,
        'col' => 8,
        'obstacle' => [
          'name' => 'hedgehog',
        ],
      ],
      58 => [
        'row' => 12,
        'col' => 10,
        'obstacle' => [
          'name' => 'wire',
          'orientation' => 1,
        ],
      ],
      59 => [
        'row' => 12,
        'col' => 12,
        'obstacle' => [
          'name' => 'wire',
          'orientation' => 1,
        ],
      ],
      60 => [
        'row' => 12,
        'col' => 14,
        'obstacle' => [
          'name' => 'hedgehog',
        ],
      ],
      61 => [
        'row' => 12,
        'col' => 16,
        'obstacle' => [
          'name' => 'hedgehog',
        ],
      ],
      62 => [
        'row' => 12,
        'col' => 18,
        'obstacle' => [
          'name' => 'wire',
          'orientation' => 1,
        ],
      ],
      63 => [
        'row' => 12,
        'col' => 20,
        'obstacle' => [
          'name' => 'hedgehog',
        ],
      ],
      64 => [
        'row' => 12,
        'col' => 22,
        'obstacle' => [
          'name' => 'wire',
          'orientation' => 1,
        ],
      ],
      65 => [
        'row' => 13,
        'col' => 11,
        'obstacle' => [
          'name' => 'hedgehog',
        ],
      ],
      66 => [
        'row' => 13,
        'col' => 17,
        'obstacle' => [
          'name' => 'hedgehog',
        ],
      ],
      67 => [
        'row' => 14,
        'col' => 2,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      68 => [
        'row' => 14,
        'col' => 4,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      69 => [
        'row' => 14,
        'col' => 6,
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      70 => [
        'row' => 14,
        'col' => 8,
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      71 => [
        'row' => 14,
        'col' => 10,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      72 => [
        'row' => 14,
        'col' => 12,
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      73 => [
        'row' => 14,
        'col' => 14,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      74 => [
        'row' => 14,
        'col' => 16,
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      75 => [
        'row' => 14,
        'col' => 18,
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      76 => [
        'row' => 14,
        'col' => 20,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      77 => [
        'row' => 15,
        'col' => 5,
        'unit' => [
          'name' => 'inf2brit',
          'badge' => 'badge1',
        ],
      ],
      78 => [
        'row' => 15,
        'col' => 7,
        'unit' => [
          'name' => 'gunbrit',
        ],
      ],
      79 => [
        'row' => 15,
        'col' => 11,
        'unit' => [
          'name' => 'inf2brit',
          'badge' => 'badge1',
        ],
      ],
      80 => [
        'row' => 15,
        'col' => 17,
        'unit' => [
          'name' => 'gunbrit',
        ],
      ],
      81 => [
        'row' => 15,
        'col' => 19,
        'unit' => [
          'name' => 'inf2brit',
          'badge' => 'badge1',
        ],
      ],
    ],
    'labels' => [
      0 => [
        'row' => 0,
        'col' => 2,
        'text' => [
          0 => clienttranslate('Orne'),
        ],
      ],
      2 => [
        'row' => 0,
        'col' => 14,
        'text' => [
          0 => clienttranslate('Caen'),
        ],
      ],
      3 => [
        'row' => 5,
        'col' => 1,
        'text' => [
          0 => clienttranslate('Canal de l\'Orne'),
        ],
      ],
      4 => [
        'row' => 7,
        'col' => 1,
        'text' => [
          0 => clienttranslate('Ouistreham'),
        ],
      ],
      6 => [
        'row' => 8,
        'col' => 18,
        'text' => [
          0 => clienttranslate('Lion-sur-Mer'),
        ],
      ],
      7 => [
        'row' => 9,
        'col' => 1,
        'text' => [
          0 => clienttranslate('Casino'),
          1 => clienttranslate('Riva Bella'),
        ],
      ],
      8 => [
        'row' => 9,
        'col' => 23,
        'text' => [
          0 => clienttranslate('Chateau'),
        ],
      ],
      9 => [
        'row' => 10,
        'col' => 12,
        'text' => [
          0 => clienttranslate('Strong-point'),
          1 => clienttranslate('Cod'),
        ],
      ],
      10 => [
        'row' => 11,
        'col' => 19,
        'text' => [
          0 => clienttranslate('Strong-point'),
          1 => clienttranslate('Trout'),
        ],
      ],
    ],
  ],
  'text' => [
    'en' => [
      'name' => clienttranslate('Sword Beach'),
      'subtitle' => clienttranslate('			'),
      'description' => clienttranslate('Axis Player [Germany]
Take 5 Command cards.

Allied Player [Great Britain]
Take 6 Command cards.
You move first.'),
      'rules' => clienttranslate('British Commonwealth Forces command rules are in effect, for British units (Nation 5 - British Commonwealth Forces).

Place a badge on the German elite infantry unit and on the Commando infantry units (Troops 2 - Specialized Units).

The Axis player is in control of the Bunkers and may claim them as a defensive position (Terrain 2 - Bunkers).

Air Rules are not in effect. The Air Sortie cards are set aside and not used in this scenario.'),
      'historical' => clienttranslate('The early hours, morning of June 6, 1944 - Men of the British 6th Airborne Division airdrop inland to capture the bridges over the Orne River and canal and prevent the German armored formations in the area between Normandy and Paris from moving west to attack the left flank of the upcoming Allied beachhead. Shortly thereafter, the 8th Brigade Group of the 3rd British Division, supported by Commandos of the 1st Special Service Brigade and the amphibious Sherman tanks of the 13th and 18th Hussars, lands on Sword beach. The Germans are about to find out how illusory the protection of ill-named strong-points Cod and Trout really are, in the face of a determined armada...

The stage is set, the battle lines are drawn, and you are in command. The rest is history.'),
      'victory' => clienttranslate('12 Medals.

Casino Riva Bella, Strong-point Trout and the Chateau are Permanent Medal Objectives for the Allied forces.

The two bridges over the Orne River and Canal form a Temporary Majority Medal Objective worth 2 Medals for whoever controls it.

The two hexes on the outskirts of Caen form a Temporary Majority Medal Objective worth 3 Medals for whoever controls them. The Axis player controls them at game start, and thus has a 3 Medals headstart.'),
    ],
  ],
];
