<?php
namespace M44\Scenarios;

$scenarios[1488] = [
  'meta_data' => [
    'scenario_id' => '1488',
    'status' => 'APPROVED',
    'can_translate' => 'false',
    'mod_date' => '2010-06-03 07:05:38',
    'ownerID' => 8860,
    'software' => 'sed 1.2',
    'create_by' => 8860,
    'create_date' => '2008-04-07 17:54:35',
    'mod_by' => 291520,
    'author_id' => 8860,
  ],
  'game_info' => [
    'front' => 'PACIFIC',
    'date_begin' => '1944-07-25',
    'date_end' => '1944-07-25',
    'type' => 'HISTORICAL',
    'starting' => 'PLAYER2',
    'side_player1' => 'AXIS',
    'side_player2' => 'ALLIES',
    'country_player1' => 'JP',
    'country_player2' => 'US',
    'cards_player1' => 5,
    'cards_player2' => 6,
    'victory_player1' => 6,
    'victory_player2' => 6,
    'victory_conditions' => [
      0 => [
        'standard' => [],
      ],
    ],
    'options' => [
      'gung_ho' => true,
      'night_visibility_rules' => true,
    ],
  ],
  'board' => [
    'type' => 'STANDARD',
    'face' => 'COUNTRY',
    'hexagons' => [
      0 => [
        'row' => 0,
        'col' => 2,
        'terrain' => [
          'name' => 'camp',
        ],
        'tags' => [
          0 => [
            'name' => 'medal1',
            'medal' => [
              'permanent' => true,
              'counts_for' => 1,
            ],
          ],
        ],
      ],
      1 => [
        'row' => 0,
        'col' => 4,
        'unit' => [
          'name' => 'infjp',
        ],
      ],
      2 => [
        'row' => 0,
        'col' => 18,
        'terrain' => [
          'name' => 'river',
          'orientation' => 3,
          'behavior' => 'FORDABLE_STREAM',
        ],
        'unit' => [
          'name' => 'infjp',
        ],
      ],
      3 => [
        'row' => 0,
        'col' => 20,
        'unit' => [
          'name' => 'infjp',
        ],
      ],
      4 => [
        'row' => 0,
        'col' => 22,
        'unit' => [
          'name' => 'infjp',
        ],
      ],
      5 => [
        'row' => 0,
        'col' => 24,
        'unit' => [
          'name' => 'infjp',
        ],
      ],
      6 => [
        'row' => 1,
        'col' => 1,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      7 => [
        'row' => 1,
        'col' => 3,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 5,
          'behavior' => 'FORDABLE_STREAM',
        ],
        'unit' => [
          'name' => 'infjp',
        ],
      ],
      8 => [
        'row' => 1,
        'col' => 5,
        'terrain' => [
          'name' => 'pjungle',
        ],
        'unit' => [
          'name' => 'infjp',
        ],
      ],
      9 => [
        'row' => 1,
        'col' => 19,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 4,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      10 => [
        'row' => 1,
        'col' => 21,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      11 => [
        'row' => 2,
        'col' => 4,
        'terrain' => [
          'name' => 'river',
          'orientation' => 3,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      12 => [
        'row' => 2,
        'col' => 6,
        'unit' => [
          'name' => 'infjp',
        ],
      ],
      13 => [
        'row' => 2,
        'col' => 8,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      14 => [
        'row' => 2,
        'col' => 10,
        'unit' => [
          'name' => 'infjp',
        ],
      ],
      15 => [
        'row' => 2,
        'col' => 12,
        'unit' => [
          'name' => 'infjp',
        ],
      ],
      16 => [
        'row' => 2,
        'col' => 14,
        'unit' => [
          'name' => 'infjp',
        ],
      ],
      17 => [
        'row' => 2,
        'col' => 16,
        'unit' => [
          'name' => 'infjp',
        ],
      ],
      18 => [
        'row' => 2,
        'col' => 18,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      19 => [
        'row' => 3,
        'col' => 1,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      20 => [
        'row' => 3,
        'col' => 3,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      21 => [
        'row' => 3,
        'col' => 5,
        'terrain' => [
          'name' => 'river',
          'orientation' => 3,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      22 => [
        'row' => 3,
        'col' => 13,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      23 => [
        'row' => 3,
        'col' => 15,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 6,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      24 => [
        'row' => 3,
        'col' => 17,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 3,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      25 => [
        'row' => 3,
        'col' => 19,
        'terrain' => [
          'name' => 'pjungle',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'infus',
        ],
      ],
      26 => [
        'row' => 3,
        'col' => 21,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      27 => [
        'row' => 3,
        'col' => 23,
        'terrain' => [
          'name' => 'hills',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'infus',
        ],
      ],
      28 => [
        'row' => 4,
        'col' => 0,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      29 => [
        'row' => 4,
        'col' => 2,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      30 => [
        'row' => 4,
        'col' => 4,
        'terrain' => [
          'name' => 'hills',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'infus',
        ],
      ],
      31 => [
        'row' => 4,
        'col' => 6,
        'terrain' => [
          'name' => 'river',
          'orientation' => 3,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      32 => [
        'row' => 4,
        'col' => 8,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      33 => [
        'row' => 4,
        'col' => 10,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      34 => [
        'row' => 4,
        'col' => 12,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      35 => [
        'row' => 4,
        'col' => 14,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      36 => [
        'row' => 4,
        'col' => 20,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      37 => [
        'row' => 5,
        'col' => 1,
        'terrain' => [
          'name' => 'price',
        ],
      ],
      38 => [
        'row' => 5,
        'col' => 3,
        'terrain' => [
          'name' => 'price',
        ],
      ],
      39 => [
        'row' => 5,
        'col' => 5,
        'terrain' => [
          'name' => 'price',
        ],
      ],
      40 => [
        'row' => 5,
        'col' => 7,
        'terrain' => [
          'name' => 'river',
          'orientation' => 3,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      41 => [
        'row' => 5,
        'col' => 9,
        'terrain' => [
          'name' => 'price',
        ],
      ],
      42 => [
        'row' => 5,
        'col' => 11,
        'terrain' => [
          'name' => 'price',
        ],
      ],
      43 => [
        'row' => 5,
        'col' => 13,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      44 => [
        'row' => 5,
        'col' => 15,
        'terrain' => [
          'name' => 'price',
        ],
      ],
      45 => [
        'row' => 5,
        'col' => 17,
        'terrain' => [
          'name' => 'price',
        ],
      ],
      46 => [
        'row' => 5,
        'col' => 19,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      47 => [
        'row' => 5,
        'col' => 21,
        'terrain' => [
          'name' => 'price',
        ],
      ],
      48 => [
        'row' => 5,
        'col' => 23,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      49 => [
        'row' => 6,
        'col' => 6,
        'unit' => [
          'name' => 'gunus',
        ],
      ],
      50 => [
        'row' => 6,
        'col' => 8,
        'terrain' => [
          'name' => 'river',
          'orientation' => 3,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      51 => [
        'row' => 6,
        'col' => 12,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      52 => [
        'row' => 6,
        'col' => 14,
        'terrain' => [
          'name' => 'hills',
        ],
        'unit' => [
          'name' => 'infus',
        ],
      ],
      53 => [
        'row' => 6,
        'col' => 16,
        'terrain' => [
          'name' => 'hills',
        ],
        'unit' => [
          'name' => 'gunus',
        ],
      ],
      54 => [
        'row' => 6,
        'col' => 18,
        'terrain' => [
          'name' => 'hills',
        ],
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      55 => [
        'row' => 6,
        'col' => 20,
        'terrain' => [
          'name' => 'price',
        ],
      ],
      56 => [
        'row' => 6,
        'col' => 22,
        'terrain' => [
          'name' => 'pjungle',
        ],
        'unit' => [
          'name' => 'infus',
        ],
      ],
      57 => [
        'row' => 7,
        'col' => 1,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      58 => [
        'row' => 7,
        'col' => 5,
        'terrain' => [
          'name' => 'pvillage',
        ],
        'unit' => [
          'name' => 'inf2us',
          'badge' => 'badge12',
        ],
      ],
      59 => [
        'row' => 7,
        'col' => 7,
        'terrain' => [
          'name' => 'pvillage',
        ],
      ],
      60 => [
        'row' => 7,
        'col' => 9,
        'terrain' => [
          'name' => 'river',
          'orientation' => 3,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      61 => [
        'row' => 7,
        'col' => 11,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      62 => [
        'row' => 7,
        'col' => 13,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      63 => [
        'row' => 7,
        'col' => 15,
        'terrain' => [
          'name' => 'phospital',
        ],
        'tags' => [
          0 => [
            'name' => 'medal7',
            'medal' => [
              'permanent' => true,
              'counts_for' => 1,
            ],
          ],
        ],
      ],
      64 => [
        'row' => 7,
        'col' => 17,
        'unit' => [
          'name' => 'gunus',
        ],
      ],
      65 => [
        'row' => 7,
        'col' => 23,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      66 => [
        'row' => 8,
        'col' => 0,
        'terrain' => [
          'name' => 'pheadquarter',
          'original_owner' => 'ALLIES',
        ],
        'tags' => [
          0 => [
            'name' => 'medal7',
            'medal' => [
              'permanent' => true,
              'counts_for' => 1,
            ],
          ],
        ],
      ],
      67 => [
        'row' => 8,
        'col' => 4,
        'unit' => [
          'name' => 'inf2us',
          'badge' => 'badge12',
        ],
      ],
      68 => [
        'row' => 8,
        'col' => 6,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      69 => [
        'row' => 8,
        'col' => 8,
        'terrain' => [
          'name' => 'pheadquarter',
          'original_owner' => 'ALLIES',
        ],
        'tags' => [
          0 => [
            'name' => 'medal7',
            'medal' => [
              'permanent' => true,
              'counts_for' => 1,
            ],
          ],
        ],
      ],
      70 => [
        'row' => 8,
        'col' => 10,
        'terrain' => [
          'name' => 'riverFR',
          'orientation' => 3,
        ],
      ],
      71 => [
        'row' => 8,
        'col' => 12,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      72 => [
        'row' => 8,
        'col' => 22,
        'unit' => [
          'name' => 'inf2us',
          'badge' => 'badge12',
        ],
      ],
      73 => [
        'row' => 8,
        'col' => 24,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
    ],
    'labels' => [
      0 => [
        'row' => 0,
        'col' => 2,
        'text' => [
          0 => clienttranslate('Labor Camp'),
        ],
      ],
      1 => [
        'row' => 5,
        'col' => 3,
        'text' => [
          0 => clienttranslate('Rice Paddies'),
        ],
      ],
      2 => [
        'row' => 5,
        'col' => 11,
        'text' => [
          0 => clienttranslate('Rice Paddies'),
        ],
      ],
      3 => [
        'row' => 5,
        'col' => 17,
        'text' => [
          0 => clienttranslate('Rice Paddies'),
        ],
      ],
      4 => [
        'row' => 7,
        'col' => 5,
        'text' => [
          0 => clienttranslate('Asan'),
        ],
      ],
      5 => [
        'row' => 7,
        'col' => 15,
        'text' => [
          0 => clienttranslate('Hospital'),
        ],
      ],
      6 => [
        'row' => 8,
        'col' => 0,
        'text' => [
          0 => clienttranslate('HQ-Supply Tents'),
        ],
      ],
      7 => [
        'row' => 8,
        'col' => 8,
        'text' => [
          0 => clienttranslate('HQ-Supply Tents'),
        ],
      ],
    ],
  ],
  'text' => [
    'en' => [
      'name' => clienttranslate('Japanese Counterattack'),
      'subtitle' => clienttranslate('Battle of Guam'),
      'historical' => clienttranslate('Lt. General Takeshi\'s counterattack plan called for a night attack on July 25/26. The goal was to split the 3rd Marines and target ammunition and supply dumps. The frontline of the 3rd Marines was stretched thin in the area and reserves were limited to a few engineer and tank units.

The fighting went on all night as the Japanese units penetrated almost to the beach and successfully destroyed supplies and equipment. At dawn, the Allied tanks and artillery could finally see their enemy and soon threw the Japanese back. Early on the 26th, General Obata was forced to report the results of his failed attack back to Headquarters in Tokyo.

The stage is set, the battle lines are drawn, and you are in command. The rest is history.'),
      'description' => clienttranslate('Japanese Player: Take 5 Command cards.

US Marine Player: Take 6 Command cards.
You move first.
'),
      'victory' => clienttranslate('6 Medals.

A Japanese unit that captures a HQ-Supply Tent or Hospital Tent hex counts as one Victory Medal. Place a Japanese Objective Medal on each of the tent hexes. The medal once gained, continues to count toward the Japanese victory, even if the unit moves off the hex or is eliminated.

An Allied unit that captures the Labor Camp counts as one Victory Medal. Place an Allied Objective Medal on this hex. The medal once gained continues to count toward the Marine victory, even if the unit moves off the hex or is eliminated.'),
      'rules' => clienttranslate('Night Attack rules are in effect (see p. 8).

Imperial Japanese Army Command rules are in effect (see p. 7).

US Marine Corps Command rules are in effect (see p. 7).

The Marine Special Forces Infantry units are Engineer units. Place an Engineer badge in the same hex with these units to distinguish them from the other units. Read p. 10 about Engineers.

HQ-Supply and Hospital Tents are explained on p. 5.
HQ-Supply and Hospital Tents special landmark rules are in effect.

Jungles are explained on p. 5.

Rice Paddies are explained on p. 6.

The Asan River is a Fordable Stream.  Fordable Stream is explained on p. 6.'),
      'bibliography' => clienttranslate('http://www.ibiblio.org/hyperwar/USA/USA-P-Marianas/USA-P-Marianas-17.html'),
    ],
  ],
];
