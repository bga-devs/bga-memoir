<?php
namespace M44\Scenarios;

$scenarios[4187] = [
  'meta_data' => [
    'status' => 'PUBLIC',
    'software' => 'sed 1.2',
    'scenario_id' => '4187',
  ],
  'game_info' => [
    'date_begin' => '1944-06-25',
    'front' => 'WESTERN',
    'type' => 'HISTORICAL',
    'starting' => 'PLAYER2',
    'side_player1' => 'AXIS',
    'side_player2' => 'ALLIES',
    'country_player1' => 'DE',
    'country_player2' => 'US',
    'cards_player1' => 5,
    'cards_player2' => 4,
    'victory_player1' => 5,
    'victory_player2' => 5,
    'operationID' => '15',
    'date_end' => '1944-06-25',
  ],
  'board' => [
    'type' => 'STANDARD',
    'face' => 'COUNTRY',
    'hexagons' => [
      0 => [
        'row' => 0,
        'col' => 2,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      1 => [
        'row' => 0,
        'col' => 4,
        'unit' => [
          'name' => 'gunger',
        ],
      ],
      2 => [
        'row' => 0,
        'col' => 6,
        'terrain' => [
          'name' => 'buildings',
        ],
        'tags' => [
          0 => [
            'name' => 'medal1',
          ],
        ],
      ],
      3 => [
        'row' => 0,
        'col' => 10,
        'terrain' => [
          'name' => 'hills',
        ],
        'unit' => [
          'name' => 'tank2ger',
          'nbr_units' => '4',
          'badge' => 'badge4',
        ],
      ],
      4 => [
        'row' => 0,
        'col' => 14,
        'terrain' => [
          'name' => 'buildings',
        ],
      ],
      5 => [
        'row' => 0,
        'col' => 18,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      6 => [
        'row' => 0,
        'col' => 22,
        'terrain' => [
          'name' => 'buildings',
        ],
      ],
      7 => [
        'row' => 0,
        'col' => 24,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      8 => [
        'row' => 1,
        'col' => 3,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      9 => [
        'row' => 1,
        'col' => 9,
        'terrain' => [
          'name' => 'hills',
        ],
        'unit' => [
          'name' => 'inf2ger',
          'badge' => 'badge4',
        ],
      ],
      10 => [
        'row' => 1,
        'col' => 17,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      11 => [
        'row' => 1,
        'col' => 19,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      12 => [
        'row' => 1,
        'col' => 21,
        'terrain' => [
          'name' => 'hedgerow',
        ],
      ],
      13 => [
        'row' => 1,
        'col' => 23,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      14 => [
        'row' => 2,
        'col' => 4,
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
      15 => [
        'row' => 2,
        'col' => 6,
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
      16 => [
        'row' => 2,
        'col' => 12,
        'terrain' => [
          'name' => 'buildings',
        ],
        'tags' => [
          0 => [
            'name' => 'medal1',
            'group' => [
              0 => 'f6',
              1 => 'g6',
              //2 => 'G7', // medal already present intresenqly in G7 otherwise will be counted twice
            ],
            //'side' => 'ALLIES',
            'medal' => [
              'counts_for' => 1,
              'majority' => true,
              'side' => ALLIES,
            ],
          ],
        ],
      ],
      17 => [
        'row' => 2,
        'col' => 16,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      18 => [
        'row' => 2,
        'col' => 20,
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      19 => [
        'row' => 2,
        'col' => 22,
        'terrain' => [
          'name' => 'hedgerow',
        ],
      ],
      20 => [
        'row' => 3,
        'col' => 11,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      21 => [
        'row' => 3,
        'col' => 13,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      22 => [
        'row' => 3,
        'col' => 17,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      23 => [
        'row' => 3,
        'col' => 23,
        'terrain' => [
          'name' => 'woods',
        ],
        'unit' => [
          'name' => 'infger',
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
        'col' => 22,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      26 => [
        'row' => 5,
        'col' => 7,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      27 => [
        'row' => 5,
        'col' => 9,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      28 => [
        'row' => 5,
        'col' => 11,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      29 => [
        'row' => 5,
        'col' => 19,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      30 => [
        'row' => 5,
        'col' => 23,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      31 => [
        'row' => 6,
        'col' => 2,
        'terrain' => [
          'name' => 'woods',
        ],
        'unit' => [
          'name' => 'infus',
        ],
      ],
      32 => [
        'row' => 6,
        'col' => 4,
        'terrain' => [
          'name' => 'woods',
        ],
        'unit' => [
          'name' => 'infus',
        ],
      ],
      33 => [
        'row' => 6,
        'col' => 10,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      34 => [
        'row' => 6,
        'col' => 12,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      35 => [
        'row' => 6,
        'col' => 14,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      36 => [
        'row' => 6,
        'col' => 20,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      37 => [
        'row' => 6,
        'col' => 22,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      38 => [
        'row' => 7,
        'col' => 3,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      39 => [
        'row' => 7,
        'col' => 5,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      40 => [
        'row' => 7,
        'col' => 9,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      41 => [
        'row' => 7,
        'col' => 11,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      42 => [
        'row' => 7,
        'col' => 13,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      43 => [
        'row' => 7,
        'col' => 15,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      44 => [
        'row' => 7,
        'col' => 17,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      45 => [
        'row' => 7,
        'col' => 21,
        'terrain' => [
          'name' => 'woods',
        ],
        'unit' => [
          'name' => 'infus',
        ],
      ],
      46 => [
        'row' => 8,
        'col' => 4,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      47 => [
        'row' => 8,
        'col' => 10,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      48 => [
        'row' => 8,
        'col' => 12,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      49 => [
        'row' => 8,
        'col' => 18,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
    ],
    'labels' => [
      0 => [
        'row' => 0,
        'col' => 6,
        'text' => [
          0 => 'Raury',
        ],
      ],
      1 => [
        'row' => 2,
        'col' => 12,
        'text' => [
          0 => 'Fontenay le Pesnel',
        ],
      ],
    ],
  ],
  'packs' => [
    'campaign' => 1,
  ],
  'text' => [
    'en' => [
      'name' => 'SECURING THE FLANK',
      'subtitle' => 'Operation Epsom',
      'historical' => clienttranslate('In preparation for Montgomery\'s Operation Epsom, the 49"’ Infantry Division, nicknamed the Polar Bears because of their arm patch, was ordered to take the town of Fontenay le Pesnel. Their second objective was to secure Raury and the heights that surrounded it. The capture of these two locations would protect the 15th Division\'s flank as they made the main push against their primary objective of Epsom - an outflanking action that would finally take Caen.

The 49" Infantry was faced with the 12“ 55 Panzer Division (Hitlerjugend) and bumped into Panzer Lehr\'s flank, bringing them into the battle. The German defenders held tough and the inexperienced division was unable to take its objective that day.

Allied command chose to move ahead anyway and Operation Epsom was kicked off on June 26 with the high ground around Raury still in the hands of the enemy. German armor and artillery overlooked the ground that the 15“ was moving over, leading to heavier resistance than Montgomery had hoped to face. 

The stage is set, the battle lines are drawn, and you are in command. The rest is history.'),
      'description' => clienttranslate('Axis Player [Germany]
Take 5 Command cards.

Allied Player [Great Britain]
Take 4 Command cards.
You move first.'),
      'victory' => clienttranslate('5 medals.

The three hexes of Fontenay le Pesnel are a Temporary Majority Medal Objective for the Allied forces.

Raury is a Temporary Medal Objective for the Allied forces.'),
      'rules' => clienttranslate('Place a badge on the Axis armor unit and Axis infantry units on the hills between Raury and Fontenay le Pesnel (Troops 2 - Specialized Units).

Air rules are optional: If used, shuffle both Air Sortie cards into the deck, at game start.
'),
      'bibliography' => clienttranslate('Memoir\'44 - Campaign Book Volume 1'),
    ],
  ],
];