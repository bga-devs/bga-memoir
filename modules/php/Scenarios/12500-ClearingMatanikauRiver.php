<?php
namespace M44\Scenarios;

$scenarios[12500] = [
  'meta_data' => [
    'number' => '6007',
    'can_translate' => false,
    'software' => 'UpdateScenario 1.2.2 from sed 1.2.1.b1',
    'scenario_id' => '12500',
  ],
  'game_info' => [
    'front' => 'PACIFIC',
    'date_begin' => '1943-01-13',
    'date_end' => '1943-01-13',
    'type' => 'HISTORICAL',
    'starting' => 'PLAYER2',
    'side_player1' => 'AXIS',
    'side_player2' => 'ALLIES',
    'country_player1' => 'JP',
    'country_player2' => 'US',
    'cards_player1' => 5,
    'cards_player2' => 6,
    'victory_player1' => 5,
    'victory_player2' => 5,
    'options' => [
      'gung_ho' => true,
    ],
  ],
  'board' => [
    'type' => 'STANDARD',
    'face' => 'COUNTRY',
    'hexagons' => [
      0 => [
        'row' => 0,
        'col' => 4,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      1 => [
        'row' => 0,
        'col' => 6,
        'tags' => [
          0 => [
            'name' => 'tag4',
            'behavior' => 'EXIT_MARKER',
            'side' => 'ALLIES',
          ],
        ],
      ],
      2 => [
        'row' => 0,
        'col' => 8,
        'unit' => [
          'name' => 'infjp',
        ],
      ],
      3 => [
        'row' => 0,
        'col' => 18,
        'tags' => [
          0 => [
            'name' => 'tag4',
            'behavior' => 'EXIT_MARKER',
            'side' => 'ALLIES',
          ],
        ],
      ],
      4 => [
        'row' => 0,
        'col' => 20,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      5 => [
        'row' => 1,
        'col' => 7,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      6 => [
        'row' => 1,
        'col' => 11,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      7 => [
        'row' => 1,
        'col' => 13,
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'gunjp',
        ],
      ],
      8 => [
        'row' => 1,
        'col' => 19,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      9 => [
        'row' => 1,
        'col' => 23,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      10 => [
        'row' => 2,
        'col' => 2,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      11 => [
        'row' => 2,
        'col' => 4,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      12 => [
        'row' => 2,
        'col' => 6,
        'terrain' => [
          'name' => 'pjungle',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
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
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infjp',
        ],
      ],
      14 => [
        'row' => 2,
        'col' => 12,
        'terrain' => [
          'name' => 'hills',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infjp',
        ],
      ],
      15 => [
        'row' => 2,
        'col' => 14,
        'terrain' => [
          'name' => 'hills',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infjp',
        ],
      ],
      16 => [
        'row' => 2,
        'col' => 16,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      17 => [
        'row' => 2,
        'col' => 20,
        'terrain' => [
          'name' => 'hills',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infjp',
        ],
      ],
      18 => [
        'row' => 2,
        'col' => 22,
        'terrain' => [
          'name' => 'hills',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infjp',
        ],
      ],
      19 => [
        'row' => 2,
        'col' => 24,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      20 => [
        'row' => 3,
        'col' => 1,
        'terrain' => [
          'name' => 'hills',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infjp',
        ],
      ],
      21 => [
        'row' => 3,
        'col' => 3,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 6,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      22 => [
        'row' => 3,
        'col' => 5,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      23 => [
        'row' => 3,
        'col' => 7,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      24 => [
        'row' => 3,
        'col' => 9,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 5,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      25 => [
        'row' => 3,
        'col' => 17,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      26 => [
        'row' => 4,
        'col' => 0,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      27 => [
        'row' => 4,
        'col' => 2,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 3,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      28 => [
        'row' => 4,
        'col' => 4,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      29 => [
        'row' => 4,
        'col' => 8,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      30 => [
        'row' => 4,
        'col' => 10,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 2,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      31 => [
        'row' => 4,
        'col' => 12,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      32 => [
        'row' => 4,
        'col' => 14,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      33 => [
        'row' => 4,
        'col' => 16,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      34 => [
        'row' => 4,
        'col' => 18,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      35 => [
        'row' => 4,
        'col' => 20,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      36 => [
        'row' => 4,
        'col' => 22,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      37 => [
        'row' => 4,
        'col' => 24,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
          'behavior' => 'FORDABLE_STREAM',
        ],
      ],
      38 => [
        'row' => 5,
        'col' => 1,
        'terrain' => [
          'name' => 'hills',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infjp',
        ],
      ],
      39 => [
        'row' => 5,
        'col' => 3,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      40 => [
        'row' => 5,
        'col' => 5,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      41 => [
        'row' => 5,
        'col' => 7,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      42 => [
        'row' => 5,
        'col' => 9,
        'unit' => [
          'name' => 'inf2us',
          'badge' => 'badge12',
        ],
      ],
      43 => [
        'row' => 5,
        'col' => 11,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      44 => [
        'row' => 5,
        'col' => 15,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      45 => [
        'row' => 5,
        'col' => 17,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      46 => [
        'row' => 5,
        'col' => 21,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      47 => [
        'row' => 6,
        'col' => 4,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      48 => [
        'row' => 6,
        'col' => 6,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      49 => [
        'row' => 6,
        'col' => 10,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      50 => [
        'row' => 6,
        'col' => 14,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      51 => [
        'row' => 6,
        'col' => 16,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      52 => [
        'row' => 6,
        'col' => 18,
        'unit' => [
          'name' => 'inf2us',
          'badge' => 'badge12',
        ],
      ],
      53 => [
        'row' => 6,
        'col' => 20,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      54 => [
        'row' => 6,
        'col' => 22,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      55 => [
        'row' => 7,
        'col' => 3,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      56 => [
        'row' => 7,
        'col' => 7,
        'terrain' => [
          'name' => 'hills',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'gunus',
        ],
      ],
      57 => [
        'row' => 7,
        'col' => 13,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      58 => [
        'row' => 7,
        'col' => 17,
        'terrain' => [
          'name' => 'hills',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'gunus',
        ],
      ],
      59 => [
        'row' => 7,
        'col' => 19,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      60 => [
        'row' => 7,
        'col' => 21,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      61 => [
        'row' => 8,
        'col' => 4,
        'terrain' => [
          'name' => 'pjungle',
        ],
      ],
      62 => [
        'row' => 8,
        'col' => 16,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
    ],
  ],
  'text' => [
    'en' => [
      'name' => clienttranslate('Clearing Matanikau River'),
      'subtitle' => clienttranslate('Guadalcanal'),
      'sdate' => clienttranslate('January 13, 1943'),
      'historical' => clienttranslate('At the start of the new year General Patch ordered the 2nd and 8th Marines to drive westward and clear the Japanese resistance from the hills overlooking the coast near Point Cruz. On January 13th, Marines assaulted the Japanese positions and after hard fighting gained the heights overlooking the Matanikau River.

The stage is set, the battle lines are drawn, and you are in command. The rest is history. 
'),
      'description' => clienttranslate('Japanese Player: Take 5 Command cards.

US Marine Player: Take 6 Command cards.
You move first.'),
      'rules' => clienttranslate('Imperial Japanese Army Command rules are in effect (see Pacific Theater p. 7).

US Marine Corps Command rules are in effect (see Pacific Theater p. 7).

The Marine Special Forces Infantry units are Engineer units. Place an Engineer badge in the same hex with these units to distinguish them from the other units. Engineers are explained on Pacific Theater p. 10.

Matanikau River is a Fordable Stream. Fordable Streams are explained on Pacific Theater p. 6.

Jungles are explained on Pacific Theater p. 5.'),
      'victory' => clienttranslate('5 Medals

Place an Exit hex token in the two hexes as indicated on the Japanese baseline. A Marine unit that exits off the Japanese\'s side of the battlefield from either of these hexes counts as one Victory Medal. The Marine unit is removed from play. Place one figure from this unit onto the Marine Medal Stand.'),
      'mod_date' => clienttranslate('2008-04-07T17:53:22'),
      'ownerID' => clienttranslate('6'),
    ],
  ],
  'equipment_packs' => [
    'allies_pack' => [
      0 => [
        'image' => [
          'weapon' => 'WEAPON_INFANTRY',
          'badge' => 'BADGE32',
        ],
        'caption' => 'Gung-Ho !',
        'cost' => 0,
        'compulsory' => true,
      ],
      1 => [
        'image' => [
          'weapon' => 'WEAPON_INFANTRY',
          'badge' => 'BADGE12',
        ],
        'caption' => 'Engineers',
        'cost' => 0,
        'compulsory' => true,
      ],
      2 => [
        'image' => [
          'feature' => 'EXIT_MARKER',
        ],
        'caption' => '',
        'cost' => 0,
        'compulsory' => true,
      ],
    ],
    'axis_pack' => [
      0 => [
        'image' => [
          'weapon' => 'WEAPON_INFANTRY',
          'badge' => 'BADGE34',
        ],
        'caption' => 'Imperial Japanese Army',
        'cost' => 0,
        'compulsory' => true,
      ],
    ],
    'base_cost' => 2,
  ],
];
