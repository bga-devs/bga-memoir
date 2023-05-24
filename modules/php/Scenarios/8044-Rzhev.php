<?php
namespace M44\Scenarios;

$scenarios[8044] = [
  'meta_data' => [
    'number' => '999',
    'software' => 'UpdateScenario 1.1.3-SNAPSHOT from brice himself',
    'scenario_id' => '8044',
  ],
  'game_info' => [
    'front' => 'EASTERN',
    'date_begin' => '1942-01-26',
    'date_end' => '1942-02-07',
    'type' => 'HISTORICAL',
    'starting' => 'PLAYER2',
    'side_player1' => 'AXIS',
    'side_player2' => 'ALLIES',
    'country_player1' => 'DE',
    'country_player2' => 'RU',
    'cards_player1' => 6,
    'cards_player2' => 4,
    'victory_player1' => 6,
    'victory_player2' => 6,
    'options' => [
      'russian_commissar_rule' => 'ALLIES',
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
          'name' => 'buildings',
          'behavior' => 'HEADQUARTER',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'tags' => [
          0 => [
            'name' => 'medal6',
            'group' => [],
            'medal' => [
              'permanent' => true,
              'counts_for' => 1,
              'nbr_hex' => 1,
            ],
          ],
        ],
        'unit' => [
          'name' => 'inf2ger',
          'badge' => 'badge4',
        ],
      ],
      1 => [
        'row' => 0,
        'col' => 6,
        'terrain' => [
          'name' => 'wcurved',
          'orientation' => 2,
        ],
      ],
      2 => [
        'row' => 0,
        'col' => 8,
        'terrain' => [
          'name' => 'wriver',
          'orientation' => 1,
        ],
        'tags' => [
          0 => [
            'name' => 'tag5',
            'behavior' => 'EXIT_MARKER',
            'group' => [
              0 => 'E9',
              1 => 'F9',
              2 => 'G9',
              3 => 'H9',
              4 => 'I9',
              5 => 'J9',
              6 => 'K9',
            ],
            'side' => 'ALLIES',
          ],
        ],
      ],
      3 => [
        'row' => 0,
        'col' => 10,
        'terrain' => [
          'name' => 'wcurved',
          'orientation' => 5,
        ],
      ],
      4 => [
        'row' => 0,
        'col' => 14,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      5 => [
        'row' => 0,
        'col' => 16,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      6 => [
        'row' => 0,
        'col' => 18,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      7 => [
        'row' => 0,
        'col' => 20,
        'terrain' => [
          'name' => 'wforest',
        ],

      ],
      8 => [
        'row' => 1,
        'col' => 9,
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'inf2ger',
          'badge' => 'badge4',
        ],
      ],
      9 => [
        'row' => 1,
        'col' => 11,
        'terrain' => [
          'name' => 'wcurved',
          'orientation' => 2,
        ],
      ],
      10 => [
        'row' => 1,
        'col' => 13,
        'terrain' => [
          'name' => 'wcurved',
          'orientation' => 5,
        ],
      ],
      11 => [
        'row' => 1,
        'col' => 15,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      12 => [
        'row' => 1,
        'col' => 21,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'inf2ger',
          'badge' => 'badge4',
        ],
      ],
      13 => [
        'row' => 2,
        'col' => 2,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      14 => [
        'row' => 2,
        'col' => 6,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      15 => [
        'row' => 2,
        'col' => 8,
        'terrain' => [
          'name' => 'wforest',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'gunger',
        ],
      ],
      16 => [
        'row' => 2,
        'col' => 10,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      17 => [
        'row' => 2,
        'col' => 12,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      18 => [
        'row' => 2,
        'col' => 14,
        'terrain' => [
          'name' => 'wriver',
          'orientation' => 3,
        ],
      ],
      19 => [
        'row' => 3,
        'col' => 7,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      20 => [
        'row' => 3,
        'col' => 9,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      21 => [
        'row' => 3,
        'col' => 15,
        'terrain' => [
          'name' => 'wriver',
          'orientation' => 3,
        ],
      ],
      22 => [
        'row' => 3,
        'col' => 19,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      23 => [
        'row' => 3,
        'col' => 21,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'inf2ger',
          'badge' => 'badge4',
        ],
      ],
      24 => [
        'row' => 4,
        'col' => 8,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      25 => [
        'row' => 4,
        'col' => 10,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      26 => [
        'row' => 4,
        'col' => 16,
        'terrain' => [
          'name' => 'wcurved',
          'orientation' => 4,
        ],
      ],
      27 => [
        'row' => 4,
        'col' => 18,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      28 => [
        'row' => 5,
        'col' => 1,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'inf2ger',
          'badge' => 'badge4',
        ],
      ],
      29 => [
        'row' => 5,
        'col' => 5,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'inf2ger',
          'badge' => 'badge4',
        ],
      ],
      30 => [
        'row' => 5,
        'col' => 9,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      31 => [
        'row' => 5,
        'col' => 13,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'inf2ger',
          'badge' => 'badge4',
        ],
      ],
      32 => [
        'row' => 5,
        'col' => 15,
        'terrain' => [
          'name' => 'wriver',
          'orientation' => 2,
        ],
      ],
      33 => [
        'row' => 5,
        'col' => 17,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      34 => [
        'row' => 5,
        'col' => 19,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'inf2ger',
          'badge' => 'badge4',
        ],
      ],
      35 => [
        'row' => 5,
        'col' => 23,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'inf2ger',
          'badge' => 'badge4',
        ],
      ],
      36 => [
        'row' => 6,
        'col' => 14,
        'terrain' => [
          'name' => 'wcurved',
          'orientation' => 1,
        ],
      ],
      37 => [
        'row' => 6,
        'col' => 16,
        'terrain' => [
          'name' => 'wforest',
        ],
      ],
      38 => [
        'row' => 7,
        'col' => 7,
        'terrain' => [
          'name' => 'wforest',
        ],
        'unit' => [
          'name' => 'tankru',
          'behavior' => 'ARMOR_MOVE_TWO',
        ],
      ],
      39 => [
        'row' => 7,
        'col' => 9,
        'terrain' => [
          'name' => 'wforest',
        ],
        'unit' => [
          'name' => 'tankru',
          'behavior' => 'ARMOR_MOVE_TWO',
        ],
      ],
      40 => [
        'row' => 7,
        'col' => 11,
        'terrain' => [
          'name' => 'wforest',
        ],
        'unit' => [
          'name' => 'tankru',
          'behavior' => 'ARMOR_MOVE_TWO',
        ],
      ],
      41 => [
        'row' => 7,
        'col' => 13,
        'terrain' => [
          'name' => 'wforest',
        ],
        'unit' => [
          'name' => 'tankru',
          'behavior' => 'ARMOR_MOVE_TWO',
        ],
      ],
      42 => [
        'row' => 7,
        'col' => 15,
        'terrain' => [
          'name' => 'wcurved',
          'orientation' => 2,
        ],
      ],
      43 => [
        'row' => 7,
        'col' => 17,
        'terrain' => [
          'name' => 'wriver',
          'orientation' => 1,
        ],
      ],
      44 => [
        'row' => 7,
        'col' => 19,
        'terrain' => [
          'name' => 'wriver',
          'orientation' => 1,
        ],
      ],
      45 => [
        'row' => 7,
        'col' => 21,
        'terrain' => [
          'name' => 'wriver',
          'orientation' => 1,
        ],
      ],
      46 => [
        'row' => 7,
        'col' => 23,
        'terrain' => [
          'name' => 'wriver',
          'orientation' => 1,
        ],
      ],
      47 => [
        'row' => 8,
        'col' => 0,
        'unit' => [
          'name' => 'infru',
        ],
      ],
      48 => [
        'row' => 8,
        'col' => 2,
        'unit' => [
          'name' => 'infru',
        ],
      ],
      49 => [
        'row' => 8,
        'col' => 4,
        'unit' => [
          'name' => 'infru',
        ],
      ],
      50 => [
        'row' => 8,
        'col' => 6,
        'unit' => [
          'name' => 'infru',
        ],
      ],
      51 => [
        'row' => 8,
        'col' => 8,
        'unit' => [
          'name' => 'gunru',
        ],
      ],
      52 => [
        'row' => 8,
        'col' => 10,
        'unit' => [
          'name' => 'infru',
        ],
      ],
      53 => [
        'row' => 8,
        'col' => 12,
        'unit' => [
          'name' => 'gunru',
        ],
      ],
      54 => [
        'row' => 8,
        'col' => 14,
        'unit' => [
          'name' => 'tankru',
          'behavior' => 'ARMOR_MOVE_TWO',
        ],
      ],
      55 => [
        'row' => 8,
        'col' => 16,
        'unit' => [
          'name' => 'infru',
        ],
      ],
      56 => [
        'row' => 8,
        'col' => 18,
        'unit' => [
          'name' => 'infru',
        ],
      ],
      57 => [
        'row' => 8,
        'col' => 20,
        'terrain' => [
          'name' => 'buildings',
        ],
        'tags' => [
          0 => [
            'name' => 'medal2',
            'group' => [],
            'medal' => [
              'permanent' => true,
              'counts_for' => 1,
              'nbr_hex' => 1,
            ],
          ],
        ],
        'unit' => [
          'name' => 'infru',
        ],
      ],
      58 => [
        'row' => 8,
        'col' => 22,
        'unit' => [
          'name' => 'infru',
        ],
      ],
      59 => [
        'row' => 8,
        'col' => 24,
        'unit' => [
          'name' => 'infru',
        ],
      ],
    ],
    'labels' => [
      0 => [
        'row' => 0,
        'col' => 2,
        'text' => [
          0 => 'Noshkino',
        ],
      ],
      1 => [
        'row' => 0,
        'col' => 22,
        'text' => [
          0 => 'Lebsino',
        ],
      ],
      2 => [
        'row' => 2,
        'col' => 22,
        'text' => [
          0 => 'Solomino',
        ],
      ],
      3 => [
        'row' => 4,
        'col' => 2,
        'text' => [
          0 => 'Kokosch',
        ],
      ],
      4 => [
        'row' => 4,
        'col' => 6,
        'text' => [
          0 => 'Opjachtino',
        ],
      ],
      5 => [
        'row' => 4,
        'col' => 14,
        'text' => [
          0 => 'Klepenino',
        ],
      ],
      6 => [
        'row' => 4,
        'col' => 20,
        'text' => [
          0 => 'Paikowo',
        ],
      ],
      7 => [
        'row' => 4,
        'col' => 24,
        'text' => [
          0 => 'Krutiki',
        ],
      ],
      8 => [
        'row' => 7,
        'col' => 17,
        'text' => [
          0 => 'Volga River',
        ],
      ],
      9 => [
        'row' => 7,
        'col' => 21,
        'text' => [
          0 => 'Sweklino',
        ],
      ],
    ],
  ],
  'text' => [
    'en' => [
      'name' => clienttranslate('Rzhev'),
      'subtitle' => clienttranslate('Sychevsk-Vyazma Operatsiya'),
      'sdate' => clienttranslate(''),
      'historical' => clienttranslate('The Soviet counter-offensive in December 1941 sent the German Wehrmacht reeling back from the outskirts of Moscow. In a pincer move on the Northern front near Rzhev, the Soviets bludgeoned their way through the Ninth Army, leaving the Germans in shambles. General of Panzer Troops Walter Model, arguably the Wehrmacht\'s best defensive tactician, was assigned to the front and miraculously rallied the German troops, closing the gap in the lines, and cutting off a number of Soviet divisions.

To reestablish contact with their isolated units, the Soviet 30th Army launched a massive attack against the German front line troops of the Das Reich\'s "Der Führer" that occupied the villages of Krutiki, Paikowo, Klepenino, Opjachtino and Kokosch. The Soviets attacked day and night through snowstorms and bitter cold, but the German elite troopers fended off the oncoming Soviets while the Panzers sealed off any breaches in the line.

The stage is set, the battle lines are drawn, and you are in command. The rest is history.'),
      'description' => clienttranslate('Axis Player
[Germany]
Take 6 Command cards.

Allied Player
[Soviet Union]
Take 4 Command cards.
You move first.
'),
      'rules' => clienttranslate('Russian Command rules are in effect for the Allied player (Nations 2 - Red Army).

Allied Armor may only move 1-2 hexes and battle.

All German Infantry are elite units (Troops 2 - Specialized Units). Badges are not required.

The river is frozen (Terrain 47 - Frozen Rivers).

Air Rules are not in effect. The Air Sortie cards are set aside and are not used in this mission.'),
      'victory' => clienttranslate('6 Medals.

The town of Sweklino is a Permanent Medal Objective for the Axis forces.

The town of Noshkino is a Permanent Medal Objective for the Allied forces. In addition the town is the German\'s HQ. HQ/Supply rules are in effect (Actions 17 - Capture HQ/Supply Tent).

Exit markers are in effect on the portion of the Axis baseline in between the 2 Exit markers, for the Allied forces.'),
      'bibliography' => clienttranslate('December 2008 Online Release.'),
      'mod_date' => clienttranslate('2008-12-19T17:47:10'),
      'ownerID' => clienttranslate('8860'),
    ],
  ],
  'equipment_packs' => [
    'allies_pack' => [
      0 => [
        'image' => [
          'feature' => 'RUSSIAN_COMMISSAR',
        ],
        'caption' => 'Russian commissar',
        'cost' => 0,
        'compulsory' => true,
      ],
      1 => [
        'image' => [
          'feature' => 'FROZEN_RIVER',
        ],
        'caption' => 'Frozen river',
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
      3 => [
        'image' => [
          'feature' => 'PERMANENT_MEDALS',
        ],
        'caption' => 'Permanent Medal',
        'cost' => 0,
        'compulsory' => true,
      ],
    ],
    'axis_pack' => [
      0 => [
        'image' => [
          'feature' => 'FROZEN_RIVER',
        ],
        'caption' => 'Frozen river',
        'cost' => 0,
        'compulsory' => true,
      ],
      1 => [
        'image' => [
          'feature' => 'PERMANENT_MEDALS',
        ],
        'caption' => 'Permanent Medal',
        'cost' => 0,
        'compulsory' => true,
      ],
      2 => [
        'image' => [
          'weapon' => 'WEAPON_INFANTRY',
          'badge' => 'BADGE4',
        ],
        'caption' => 'Elite Troops',
        'cost' => 0,
        'compulsory' => true,
      ],
      3 => [
        'image' => [
          'feature' => 'CAPTURE_HQ',
        ],
        'caption' => 'Capture HQ',
        'cost' => 0,
        'compulsory' => true,
      ],
    ],
    'base_cost' => 2,
  ],
];
