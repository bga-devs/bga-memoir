<?php
namespace M44\Scenarios;

$scenarios[3496] = [
  'meta_data' => [
    'status' => 'PRIVATE',
    'software' => 'sed 1.2',
    'scenario_id' => '3496',
  ],
  'game_info' => [
    'date_begin' => '1940-05-13',
    'front' => 'WESTERN',
    'type' => 'HISTORICAL',
    'starting' => 'PLAYER1',
    'side_player1' => 'AXIS',
    'side_player2' => 'ALLIES',
    'country_player1' => 'DE',
    'country_player2' => 'FR',
    'cards_player1' => 6,
    'cards_player2' => 5,
    'victory_player1' => 12,
    'victory_player2' => 12,
    'options' => [
      'must_have_unit_exit' => [
        'side' => 'AXIS',
        'min_nbr_units' => 1
        ]
      ]
  ],
  'board' => [
    'type' => 'BRKTHRU',
    'face' => 'COUNTRY',
    'hexagons' => [
      0 => [
        'row' => 0,
        'col' => 0,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      1 => [
        'row' => 0,
        'col' => 4,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      2 => [
        'row' => 0,
        'col' => 6,
        'unit' => [
          'name' => 'gunger',
        ],
      ],
      3 => [
        'row' => 0,
        'col' => 8,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      4 => [
        'row' => 0,
        'col' => 10,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      5 => [
        'row' => 0,
        'col' => 14,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      6 => [
        'row' => 0,
        'col' => 18,
        'unit' => [
          'name' => 'gunger',
          'badge' => 'badge35',
        ],
      ],
      7 => [
        'row' => 0,
        'col' => 20,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      8 => [
        'row' => 0,
        'col' => 22,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      9 => [
        'row' => 0,
        'col' => 24,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      10 => [
        'row' => 1,
        'col' => 1,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      11 => [
        'row' => 1,
        'col' => 3,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      12 => [
        'row' => 1,
        'col' => 5,
        'terrain' => [
          'name' => 'woods',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      13 => [
        'row' => 1,
        'col' => 7,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      14 => [
        'row' => 1,
        'col' => 11,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      15 => [
        'row' => 1,
        'col' => 13,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      16 => [
        'row' => 1,
        'col' => 15,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      17 => [
        'row' => 1,
        'col' => 17,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      18 => [
        'row' => 1,
        'col' => 19,
        'terrain' => [
          'name' => 'woods',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      19 => [
        'row' => 1,
        'col' => 21,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      20 => [
        'row' => 4,
        'col' => 0,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      21 => [
        'row' => 4,
        'col' => 2,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 5,
        ],
      ],
      22 => [
        'row' => 4,
        'col' => 8,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'inf2us',
        ],
      ],
      23 => [
        'row' => 4,
        'col' => 12,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'inf2us',
        ],
      ],
      24 => [
        'row' => 4,
        'col' => 16,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'inf2us',
        ],
      ],
      25 => [
        'row' => 4,
        'col' => 22,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 6,
        ],
      ],
      26 => [
        'row' => 4,
        'col' => 24,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      27 => [
        'row' => 5,
        'col' => 1,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'inf2us',
        ],
      ],
      28 => [
        'row' => 5,
        'col' => 3,
        'terrain' => [
          'name' => 'river',
          'orientation' => 3,
        ],
        'rect_terrain' => [
          'name' => 'bridge',
          'orientation' => 3,
        ],
      ],
      29 => [
        'row' => 5,
        'col' => 21,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
        'rect_terrain' => [
          'name' => 'bridge',
          'orientation' => 5,
        ],
      ],
      30 => [
        'row' => 5,
        'col' => 23,
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'inf2us',
        ],
      ],
      31 => [
        'row' => 6,
        'col' => 4,
        'terrain' => [
          'name' => 'river',
          'orientation' => 3,
        ],
      ],
      32 => [
        'row' => 6,
        'col' => 8,
        'terrain' => [
          'name' => 'buildings',
        ],
      ],
      33 => [
        'row' => 6,
        'col' => 12,
        'terrain' => [
          'name' => 'buildings',
        ],
      ],
      34 => [
        'row' => 6,
        'col' => 20,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
      ],
      35 => [
        'row' => 7,
        'col' => 1,
        'terrain' => [
          'name' => 'buildings',
        ],
      ],
      36 => [
        'row' => 7,
        'col' => 3,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      37 => [
        'row' => 7,
        'col' => 5,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 4,
        ],
      ],
      38 => [
        'row' => 7,
        'col' => 11,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      39 => [
        'row' => 7,
        'col' => 17,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      40 => [
        'row' => 7,
        'col' => 19,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
      ],
      41 => [
        'row' => 7,
        'col' => 21,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      42 => [
        'row' => 7,
        'col' => 23,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      43 => [
        'row' => 8,
        'col' => 4,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
      ],
      44 => [
        'row' => 8,
        'col' => 6,
        'terrain' => [
          'name' => 'buildings',
        ],
      ],
      45 => [
        'row' => 8,
        'col' => 12,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      46 => [
        'row' => 9,
        'col' => 3,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 1,
        ],
      ],
      47 => [
        'row' => 9,
        'col' => 5,
        'terrain' => [
          'name' => 'woods',
        ],
        'unit' => [
          'name' => 'gunus',
        ],
      ],
      48 => [
        'row' => 9,
        'col' => 13,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      49 => [
        'row' => 9,
        'col' => 17,
        'obstacle' => [
          'name' => 'hedgehog',
        ],
      ],
      50 => [
        'row' => 9,
        'col' => 19,
        'obstacle' => [
          'name' => 'hedgehog',
        ],
      ],
      51 => [
        'row' => 9,
        'col' => 21,
        'obstacle' => [
          'name' => 'hedgehog',
        ],
      ],
      52 => [
        'row' => 10,
        'col' => 8,
        'terrain' => [
          'name' => 'church',
        ],
      ],
      53 => [
        'row' => 10,
        'col' => 10,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      54 => [
        'row' => 10,
        'col' => 16,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'inf2us',
        ],
      ],
      55 => [
        'row' => 11,
        'col' => 21,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      56 => [
        'row' => 12,
        'col' => 0,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      57 => [
        'row' => 12,
        'col' => 2,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      58 => [
        'row' => 12,
        'col' => 4,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      59 => [
        'row' => 12,
        'col' => 6,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      60 => [
        'row' => 12,
        'col' => 8,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
        'rect_terrain' => [
          'name' => 'bridge',
          'orientation' => 1,
        ],
      ],
      61 => [
        'row' => 12,
        'col' => 10,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 5,
        ],
      ],
      62 => [
        'row' => 12,
        'col' => 20,
        'obstacle' => [
          'name' => 'hedgehog',
        ],
      ],
      63 => [
        'row' => 12,
        'col' => 22,
        'obstacle' => [
          'name' => 'hedgehog',
        ],
      ],
      64 => [
        'row' => 12,
        'col' => 24,
        'obstacle' => [
          'name' => 'hedgehog',
        ],
      ],
      65 => [
        'row' => 13,
        'col' => 1,
        'obstacle' => [
          'name' => 'hedgehog',
        ],
      ],
      66 => [
        'row' => 13,
        'col' => 3,
        'obstacle' => [
          'name' => 'hedgehog',
        ],
      ],
      67 => [
        'row' => 13,
        'col' => 5,
        'obstacle' => [
          'name' => 'hedgehog',
        ],
      ],
      68 => [
        'row' => 13,
        'col' => 9,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'inf2us',
        ],
      ],
      69 => [
        'row' => 13,
        'col' => 11,
        'terrain' => [
          'name' => 'river',
          'orientation' => 3,
        ],
      ],
      70 => [
        'row' => 13,
        'col' => 13,
        'obstacle' => [
          'name' => 'hedgehog',
        ],
      ],
      71 => [
        'row' => 13,
        'col' => 15,
        'obstacle' => [
          'name' => 'hedgehog',
        ],
      ],
      72 => [
        'row' => 13,
        'col' => 17,
        'obstacle' => [
          'name' => 'hedgehog',
        ],
      ],
      73 => [
        'row' => 14,
        'col' => 2,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      74 => [
        'row' => 14,
        'col' => 4,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      75 => [
        'row' => 14,
        'col' => 6,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      76 => [
        'row' => 14,
        'col' => 12,
        'terrain' => [
          'name' => 'river',
          'orientation' => 3,
        ],
      ],
      77 => [
        'row' => 14,
        'col' => 14,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      78 => [
        'row' => 15,
        'col' => 5,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'infus',
        ],
      ],
      79 => [
        'row' => 15,
        'col' => 15,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      80 => [
        'row' => 15,
        'col' => 17,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'infus',
        ],
      ],
      81 => [
        'row' => 15,
        'col' => 21,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      82 => [
        'row' => 16,
        'col' => 20,
        'tags' => [
            0 => [
              'name' => 'tag4',
              'behavior' => 'EXIT_MARKER',
              'group' => [],
              'side' => \AXIS,
            ],
          ],
      ],
    ],
    'labels' => [
      0 => [
        'row' => 1,
        'col' => 13,
        'text' => [
          0 => 'Hannut',
        ],
      ],
      1 => [
        'row' => 4,
        'col' => 0,
        'text' => [
          0 => 'La petiteGuette',
        ],
      ],
      2 => [
        'row' => 4,
        'col' => 8,
        'text' => [
          0 => 'Wansin',
        ],
      ],
      3 => [
        'row' => 4,
        'col' => 12,
        'text' => [
          0 => 'Thisnes',
        ],
      ],
      4 => [
        'row' => 4,
        'col' => 16,
        'text' => [
          0 => 'Crehen',
        ],
      ],
      5 => [
        'row' => 5,
        'col' => 1,
        'text' => [
          0 => 'Orp',
        ],
      ],
      6 => [
        'row' => 6,
        'col' => 8,
        'text' => [
          0 => 'Jandrenouille',
        ],
      ],
      7 => [
        'row' => 6,
        'col' => 12,
        'text' => [
          0 => 'Merdorp',
        ],
      ],
      8 => [
        'row' => 7,
        'col' => 1,
        'text' => [
          0 => 'Mariles',
        ],
      ],
      9 => [
        'row' => 7,
        'col' => 19,
        'text' => [
          0 => 'La Mehaigne',
        ],
      ],
      10 => [
        'row' => 8,
        'col' => 6,
        'text' => [
          0 => 'Jandrain',
        ],
      ],
      11 => [
        'row' => 10,
        'col' => 8,
        'text' => [
          0 => 'Autre-Eglise',
        ],
      ],
      12 => [
        'row' => 10,
        'col' => 16,
        'text' => [
          0 => 'Ramillies',
        ],
      ],
      13 => [
        'row' => 13,
        'col' => 9,
        'text' => [
          0 => 'Perwez',
        ],
      ],
      14 => [
        'row' => 15,
        'col' => 5,
        'text' => [
          0 => 'Saint-Trond',
        ],
      ],
      15 => [
        'row' => 15,
        'col' => 17,
        'text' => [
          0 => 'Grand-Leez',
        ],
      ],
      16 => [
        'row' => 16,
        'col' => 20,
        'text' => [
          0 => 'Vers Gembloux',
        ],
      ],
    ],
  ],
  'packs' => [
    'terrain' => 1,
    'pacific' => 1,
  ],
  'text' => [
    'en' => [
      'name' => clienttranslate('Breakthrough to Gembloux'),
      'subtitle' => clienttranslate('General Prioux\'s Cavalry corps faces the Panzers'),
      'description' => clienttranslate('Axis player (Germany): Take 6 Command cards. You move first.
Allied player (France): Take 5 Command cards.'),
      'rules' => clienttranslate('- The Air Sortie card is unusable by the French player.
    Discard it and draw a new Command card instead.
    - Most of the French infantry units are motorized ; play them like Special Forces infantry (Troops 2 - Specialized Units).'),
      'historical' => clienttranslate('Hannut, Belgium - May 12, 1940. Adopting the Dyle Plan, the cavalry corps of Gen. Prioux moves into Belgium, ahead of Gen. Blanchard\'s 1st Army to confront the German invaders. Near Hannut, French scout units find themselves facing incoming fire from frontline German units. From there on, their mission is to hold back or at least delay the Germans - hoping to gain time for the slower Allied corps elements deploying along the Dyle. The two light motorized divisions (DLM) of Gen. Prioux bear the brunt of the assault, facing two formidable Panzer divisions and five supporting infantry divisions. Despite this unequal match of forces, the 2nd and 3rd DLM manage to hold the Germans back for two days, saving the Dyle Plan. On May 14, decimated but their mission accomplished, the DLMs withdraw to the rear. The stage is set, the battle lines are drawn, and you are in command. The rest is history.'),
      'victory' => clienttranslate('Axis player: 12 Medals, with at least one collected by a German unit exiting the board through the Exit hex toward Gembloux.
        Allied player: 12 Medals.'),
    ],
  ],
];