<?php
namespace M44\Scenarios;

$scenarios[1409] = [
  'meta_data' => [
    'can_translate' => 'false',
    'software' => 'sed 1.2',
    'scenario_id' => '1409',
    'create_by' => 8860,
    'create_date' => '2008-04-08 16:03:05',
    'mod_by' => 6,
    'mod_date' => '2011-05-17 18:48:51',
    'ownerID' => 8860,
    'author_id' => 8860,
    'status' => 'APPROVED',
  ],
  'game_info' => [
    'front' => 'EASTERN',
    'date_begin' => '1939-12-16',
    'date_end' => '1939-12-16',
    'type' => 'HISTORICAL',
    'starting' => 'PLAYER1',
    'side_player1' => 'AXIS',
    'side_player2' => 'ALLIES',
    'country_player1' => 'FI',
    'country_player2' => 'RU',
    'cards_player1' => 6,
    'cards_player2' => 4,
    'victory_player1' => 6,
    'victory_player2' => 6,
    'expert_mode' => 0,
    'options' => [
      'deck_name' => 'AIR_POWER_AS_ARTILLERY_BOMBARD_DECK',
      'russian_commissar_rule' => 'ALLIES',
    ],
    'victory' => [
      'condition' => [
        [
          'group_sudden_death' => [
            'side' => 'AXIS',
            'number' => 3,
            'group' => ['c4', 'F5', 'f4', 'F3'],
          ],
        ],
      ],
    ],
  ],
  'board' => [
    'type' => 'STANDARD',
    'face' => 'WINTER',
    'hexagons' => [
      0 => [
        'row' => 0,
        'col' => 4,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      1 => [
        'row' => 0,
        'col' => 8,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      2 => [
        'row' => 0,
        'col' => 10,
        'unit' => [
          'name' => 'inf2ger',
          'nbr_units' => 3,
          'badge' => 'badge30',
        ],
      ],
      3 => [
        'row' => 0,
        'col' => 12,
        'terrain' => [
          'name' => 'wcurved',
          'orientation' => 4,
        ],
      ],
      4 => [
        'row' => 0,
        'col' => 22,
        'unit' => [
          'name' => 'gunger',
        ],
      ],
      5 => [
        'row' => 1,
        'col' => 1,
        'unit' => [
          'name' => 'inf2ger',
          'nbr_units' => 3,
          'badge' => 'badge30',
        ],
      ],
      6 => [
        'row' => 1,
        'col' => 3,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      7 => [
        'row' => 1,
        'col' => 7,
        'unit' => [
          'name' => 'infru',
        ],
      ],
      8 => [
        'row' => 1,
        'col' => 9,
        'terrain' => [
          'name' => 'whill',
        ],
      ],
      9 => [
        'row' => 1,
        'col' => 11,
        'terrain' => [
          'name' => 'wriver',
          'orientation' => 2,
        ],
      ],
      10 => [
        'row' => 1,
        'col' => 13,
        'unit' => [
          'name' => 'inf2ger',
          'nbr_units' => 3,
          'badge' => 'badge30',
        ],
      ],
      11 => [
        'row' => 1,
        'col' => 17,
        'unit' => [
          'name' => 'inf2ger',
          'nbr_units' => 3,
          'badge' => 'badge30',
        ],
      ],
      12 => [
        'row' => 1,
        'col' => 21,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      13 => [
        'row' => 1,
        'col' => 23,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      14 => [
        'row' => 2,
        'col' => 0,
        'unit' => [
          'name' => 'inf2ger',
          'nbr_units' => 3,
          'badge' => 'badge30',
        ],
      ],
      15 => [
        'row' => 2,
        'col' => 2,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      16 => [
        'row' => 2,
        'col' => 6,
        'unit' => [
          'name' => 'tankru',
        ],
      ],
      17 => [
        'row' => 2,
        'col' => 8,
        'terrain' => [
          'name' => 'whill',
        ],
      ],
      18 => [
        'row' => 2,
        'col' => 10,
        'terrain' => [
          'name' => 'wriver',
          'orientation' => 2,
        ],
      ],
      19 => [
        'row' => 2,
        'col' => 18,
        'terrain' => [
          'name' => 'whill',
        ],
      ],
      20 => [
        'row' => 3,
        'col' => 9,
        'terrain' => [
          'name' => 'wriver',
          'orientation' => 2,
        ],
      ],
      21 => [
        'row' => 3,
        'col' => 15,
        'terrain' => [
          'name' => 'whill',
        ],
      ],
      22 => [
        'row' => 3,
        'col' => 17,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      23 => [
        'row' => 3,
        'col' => 21,
        'terrain' => [
          'name' => 'whill',
        ],
      ],
      24 => [
        'row' => 3,
        'col' => 23,
        'terrain' => [
          'name' => 'wcurved',
          'orientation' => 6,
        ],
      ],
      25 => [
        'row' => 4,
        'col' => 0,
        'terrain' => [
          'name' => 'wforest',
        ],
        'unit' => [
          'name' => 'inf2ger',
          'nbr_units' => 3,
          'badge' => 'badge30',
        ],
      ],
      26 => [
        'row' => 4,
        'col' => 6,
        'rect_terrain' => [
          'name' => 'wbunker',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infru',
        ],
      ],
      27 => [
        'row' => 4,
        'col' => 8,
        'terrain' => [
          'name' => 'wcurved',
          'orientation' => 1,
        ],
      ],
      28 => [
        'row' => 4,
        'col' => 10,
        'terrain' => [
          'name' => 'wvillage',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'infru',
        ],
      ],
      29 => [
        'row' => 4,
        'col' => 18,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      30 => [
        'row' => 4,
        'col' => 20,
        'terrain' => [
          'name' => 'wcurved',
          'orientation' => 6,
        ],
      ],
      31 => [
        'row' => 4,
        'col' => 22,
        'terrain' => [
          'name' => 'wcurved',
          'orientation' => 3,
        ],
      ],
      32 => [
        'row' => 4,
        'col' => 24,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      33 => [
        'row' => 5,
        'col' => 5,
        'terrain' => [
          'name' => 'wvillage',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'infru',
        ],
      ],
      34 => [
        'row' => 5,
        'col' => 7,
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'gunru',
        ],
      ],
      35 => [
        'row' => 5,
        'col' => 9,
        'unit' => [
          'name' => 'infru',
        ],
      ],
      36 => [
        'row' => 5,
        'col' => 11,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'infru',
        ],
      ],
      37 => [
        'row' => 5,
        'col' => 19,
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      38 => [
        'row' => 6,
        'col' => 0,
        'terrain' => [
          'name' => 'wriver',
          'orientation' => 1,
        ],
      ],
      39 => [
        'row' => 6,
        'col' => 2,
        'terrain' => [
          'name' => 'wriver',
          'orientation' => 1,
        ],
      ],
      40 => [
        'row' => 6,
        'col' => 4,
        'terrain' => [
          'name' => 'wriver',
          'orientation' => 1,
        ],
      ],
      41 => [
        'row' => 6,
        'col' => 6,
        'terrain' => [
          'name' => 'wriver',
          'orientation' => 1,
        ],
      ],
      42 => [
        'row' => 6,
        'col' => 8,
        'terrain' => [
          'name' => 'wcurved',
          'orientation' => 5,
        ],
      ],
      43 => [
        'row' => 6,
        'col' => 10,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'infru',
        ],
      ],
      44 => [
        'row' => 6,
        'col' => 14,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      45 => [
        'row' => 6,
        'col' => 18,
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      46 => [
        'row' => 7,
        'col' => 3,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      47 => [
        'row' => 7,
        'col' => 5,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      48 => [
        'row' => 7,
        'col' => 7,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      49 => [
        'row' => 7,
        'col' => 9,
        'terrain' => [
          'name' => 'wriver',
          'orientation' => 3,
        ],
      ],
      50 => [
        'row' => 7,
        'col' => 13,
        'terrain' => [
          'name' => 'whill',
        ],
      ],
      51 => [
        'row' => 7,
        'col' => 15,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      52 => [
        'row' => 7,
        'col' => 17,
        'terrain' => [
          'name' => 'wriver',
          'orientation' => 2,
        ],
      ],
      53 => [
        'row' => 7,
        'col' => 23,
        'unit' => [
          'name' => 'infru',
        ],
      ],
      54 => [
        'row' => 8,
        'col' => 6,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      55 => [
        'row' => 8,
        'col' => 8,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      56 => [
        'row' => 8,
        'col' => 10,
        'terrain' => [
          'name' => 'wriver',
          'orientation' => 3,
        ],
      ],
      57 => [
        'row' => 8,
        'col' => 14,
        'unit' => [
          'name' => 'inf2ger',
          'nbr_units' => 3,
          'badge' => 'badge30',
        ],
      ],
      58 => [
        'row' => 8,
        'col' => 16,
        'terrain' => [
          'name' => 'wcurved',
          'orientation' => 1,
        ],
      ],
      59 => [
        'row' => 8,
        'col' => 18,
        'terrain' => [
          'name' => 'whill',
        ],
      ],
      60 => [
        'row' => 8,
        'col' => 20,
        'unit' => [
          'name' => 'infru',
        ],
      ],
      61 => [
        'row' => 8,
        'col' => 22,
        'unit' => [
          'name' => 'infru',
        ],
      ],
      62 => [
        'row' => 8,
        'col' => 24,
        'unit' => [
          'name' => 'tankru',
        ],
      ],
    ],
    'labels' => [
      0 => [
        'row' => 2,
        'col' => 10,
        'text' => [
          0 => clienttranslate('Frozen Lake'),
        ],
      ],
      1 => [
        'row' => 3,
        'col' => 5,
        'text' => [
          0 => clienttranslate('Field Bunker'),
        ],
      ],
      2 => [
        'row' => 4,
        'col' => 12,
        'text' => [
          0 => clienttranslate('Suomussalmi'),
        ],
      ],
      3 => [
        'row' => 4,
        'col' => 20,
        'text' => [
          0 => clienttranslate('Frozen Lake'),
        ],
      ],
      4 => [
        'row' => 5,
        'col' => 3,
        'text' => [
          0 => clienttranslate('Hulkoniemi'),
        ],
      ],
      5 => [
        'row' => 5,
        'col' => 17,
        'text' => [
          0 => clienttranslate('Makinen\'s Roadblock'),
        ],
      ],
      6 => [
        'row' => 6,
        'col' => 2,
        'text' => [
          0 => clienttranslate('Frozen Lake'),
        ],
      ],
      7 => [
        'row' => 7,
        'col' => 9,
        'text' => [
          0 => clienttranslate('Frozen Lake'),
        ],
      ],
      8 => [
        'row' => 7,
        'col' => 17,
        'text' => [
          0 => clienttranslate('Frozen Lake'),
        ],
      ],
    ],
  ],
  'text' => [
    'en' => [
      'name' => clienttranslate('Suomussalmi'),
      'subtitle' => clienttranslate('Russo-Finnish War'),
      'historical' => clienttranslate('The Soviet Army crossed the border into Finland in the last days of November, 1939. On December 8th, they reached the lightly defended village of Suomussalmi. The next day, Colonel Hjalmar Siilasvuo, a veteran of WWI, brought in reinforcements and took command of the Finnish defenses. His mission was to destroy the Russian forces and force them out of the village - a tall order, as the enemy was well supplied and numerically superior.

One of Siilasvuo\'s first orders was for Captain J. A. Makinen to set up a roadblock to slow the continuing advance of the Russian 44th Division. While the roadblock operation was being developed, Siilasvuo launched an attack against the Russian positions in and around Suomussalmi. The Soviets, however were too well entrenched and little headway was made in the first few days of the battle.

In time, the Finnish ski troops, fighting on home ground, were able to slowly tighten the ring around the villages and by the first week of January had defeated the larger Soviet force.

The stage is set, the battle lines are drawn, and you are in command. The rest is history.
'),
      'description' => clienttranslate('Finnish Player: Take 6 Command cards.
You move first.

Russian Player: Take 4 Command cards.
'),
      'victory' => clienttranslate('6 Medals

If Finnish units occupy 3 of the 4 town hexes at the end of their turn, they win immediately.'),
      'rules' => clienttranslate('The Air Power card is played as an Artillery Bombard Tactic card: \'Artillery battles twice or moves up to 3 hexes\'.

The bunker is a Field Bunker (p.5).

The River represents the Frozen lakes in the area. Refer to p.3 about frozen rivers.

The Finnish Special Forces Infantry are Ski troop units. Place a Finnish badge in the same hex with these units to distinguish them from the other units. These units only have 3 figures. See p.6 for more details about Ski Troops.

Russian Command rules are in effect (see p.3).'),
    ],
  ],
];
