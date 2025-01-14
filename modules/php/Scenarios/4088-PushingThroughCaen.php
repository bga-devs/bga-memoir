<?php
namespace M44\Scenarios;

$scenarios[4088] = [
  'meta_data' => [
    'software' => 'sed2',
    'owner' => [
      'id' => 1918554,
      'login' => 'player1918554',
    ],
    'author' => [
      'id' => 1918554,
      'login' => 'player1918554',
    ],
    'status' => 'private',
    'mod_date' => '2024-11-01 22:32:23',
    'mod_by' => 1918554,
    'id' => 25575,
    'number' => NULL,
    'can_translate' => false,
    'scenario_id' => 4088,
  ],
  'game_info' => [
    'front' => 'western',
    'type' => 'historical',
    'starting' => 'PLAYER2',
    'side_player1' => 'AXIS',
    'side_player2' => 'ALLIES',
    'country_player1' => 'DE',
    'country_player2' => 'US',
    'cards_player1' => 4,
    'cards_player2' => 6,
    'victory_player1' => 5,
    'victory_player2' => 5,
    'operation' => NULL,
    'expert' => false,
    'date_begin' => '1944-07-19',
    'date_end' => '1944-07-22',
    'operationID' => 8,
  ],
  'board' => [
    'type' => 'standard',
    'face' => 'country',
    'hexagons' => [
      0 => [
        'col' => 3,
        'row' => 1,
        'terrain' => [
          'name' => 'woods',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      1 => [
        'col' => 9,
        'row' => 1,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      2 => [
        'col' => 16,
        'row' => 0,
        'terrain' => [
          'name' => 'hills',
        ],
        'tags' => [
          0 => [
            'name' => 'medal1',
            'orientation' => 1,
          ],
        ],
      ],
      3 => [
        'col' => 23,
        'row' => 1,
        'terrain' => [
          'name' => 'woods',
        ],
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      4 => [
        'col' => 6,
        'row' => 2,
        'terrain' => [
          'name' => 'buildings',
        ],
        'tags' => [
          0 => [
            'name' => 'medal1',
            'group' => [
              0 => 'E7',
              //1 => 'D7', // medal already present intresenqly in G7 otherwise will be counted twice
            ],                   
            'medal' => [
              'counts_for' => 1,
              'majority' => true,
              'side' => ALLIES,
            ],
          ],
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      5 => [
        'col' => 8,
        'row' => 2,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      6 => [
        'col' => 6,
        'row' => 4,
        'terrain' => [
          'name' => 'woods',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      7 => [
        'col' => 2,
        'row' => 4,
        'terrain' => [
          'name' => 'woods',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      8 => [
        'col' => 1,
        'row' => 5,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      9 => [
        'col' => 2,
        'row' => 6,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 6,
        ],
      ],
      10 => [
        'col' => 1,
        'row' => 7,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
      ],
      11 => [
        'col' => 0,
        'row' => 8,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
      ],
      12 => [
        'col' => 12,
        'row' => 6,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      13 => [
        'col' => 4,
        'row' => 6,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
        'rect_terrain' => [
          'name' => 'bridge',
          'orientation' => 6,
        ],
      ],
      14 => [
        'col' => 6,
        'row' => 6,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      15 => [
        'col' => 8,
        'row' => 6,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      16 => [
        'col' => 10,
        'row' => 6,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      17 => [
        'col' => 14,
        'row' => 6,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
        'rect_terrain' => [
          'name' => 'bridge',
          'orientation' => 6,
        ],
      ],
      18 => [
        'col' => 16,
        'row' => 6,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      19 => [
        'col' => 18,
        'row' => 6,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      20 => [
        'col' => 20,
        'row' => 6,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      21 => [
        'col' => 22,
        'row' => 6,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 3,
        ],
      ],
      22 => [
        'col' => 23,
        'row' => 5,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
        'rect_terrain' => [
          'name' => 'bridge',
          'orientation' => 6,
        ],
      ],
      23 => [
        'col' => 24,
        'row' => 4,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 6,
        ],
      ],
      24 => [
        'col' => 22,
        'row' => 4,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      25 => [
        'col' => 21,
        'row' => 5,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      26 => [
        'col' => 19,
        'row' => 3,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      27 => [
        'col' => 18,
        'row' => 4,
        'terrain' => [
          'name' => 'woods',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      28 => [
        'col' => 10,
        'row' => 4,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      29 => [
        'col' => 12,
        'row' => 4,
        'terrain' => [
          'name' => 'buildings',
        ],
      ],
      30 => [
        'col' => 13,
        'row' => 5,
        'terrain' => [
          'name' => 'buildings',
        ],
      ],
      31 => [
        'col' => 15,
        'row' => 7,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'infus',
        ],
      ],
      32 => [
        'col' => 17,
        'row' => 7,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'infus',
        ],
      ],
      33 => [
        'col' => 3,
        'row' => 7,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      34 => [
        'col' => 4,
        'row' => 8,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      35 => [
        'col' => 6,
        'row' => 8,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      36 => [
        'col' => 7,
        'row' => 7,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      37 => [
        'col' => 12,
        'row' => 8,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      38 => [
        'col' => 10,
        'row' => 8,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      39 => [
        'col' => 14,
        'row' => 8,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      40 => [
        'col' => 19,
        'row' => 7,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      41 => [
        'col' => 21,
        'row' => 7,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      42 => [
        'col' => 22,
        'row' => 8,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      43 => [
        'col' => 24,
        'row' => 6,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      44 => [
        'col' => 14,
        'row' => 2,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      45 => [
        'col' => 10,
        'row' => 0,
        'unit' => [
          'name' => 'gunger',
        ],
      ],
      46 => [
        'col' => 7,
        'row' => 1,
      ],
    ],
    'groups' => [
    ],
    'labels' => [
      0 => [
        'col' => 16,
        'row' => 0,
        'text' => [
          0 => 'Verrières Ridge',
        ],
      ],
      1 => [
        'col' => 7,
        'row' => 1,
        'text' => [
          0 => 'Collombelles',
        ],
      ],
      2 => [
        'col' => 10,
        'row' => 6,
        'text' => [
          0 => 'Orne River',
        ],
      ],
    ],
  ],
  'packs' => [
  ],
  'text' => [
    'en' => [
      'name' => clienttranslate('PUSHING THROUGH CAEN'),
      'sdate' => '',
      'subtitle' => '',
      'historical' => clienttranslate('Battle of Normandy Campaign - Taking Caen (Operation Atlantic):

Caen proved a tougher challenge than the Allies had expected and German forces tenaciously defended every piece of it. After withdrawing across the Orne River to the south, the Germans set up a hasty defense in the part of Caen they still controlled. The next Allied objective was Verrières Ridge which overlooked Caen from the south and would aid in the capture of the city.

Operation Atlantic was the codename for the Canadian drive to take Verrières Ridge and was conducted in conjunction with Operation Goodwood, being carried out by their British comrades on the left. The Canadian II Corps was operational as a formation for the first time in Normandy and the ill-fated 2nd Division that suffered the terrible defeat at Dieppe led the way in this attack. 

After successfully crossing the Orne River on July 20, the 2nd Canadian Infantry captured Colombelles. The 4th and 6th Canadian Infantry Brigades continued the push until they engaged the 12th and 1st SS Panzer Divisions near the Verrières Ridge. Following initial gains in the face of stubborn German defenders, the Canadian infantry was driven back by a powerful counter-attack and suffered heavy casualties. With help from the 3rd Canadian Infantry Division the line was stabilized along the ridge but further progress was impossible until General Guy Simonds initiated Operation Spring on July 25.

The stage is set, the battle lines drawn, and you are in command. The rest is history.'),
      'description' => clienttranslate('Axis Player [Germany]: Take 4 Command cards.

Allied Player [Canada]: Take 6 Command cards. You move first.'),
      'victory' => clienttranslate('5 Medals.

Verrières Ridge is a Temporary Medal Objective for the Allied forces.

The two town hexes of Colombelles are a Temporary Majority Medal Objective worth 1 medal for the Allied forces'),
      'rules' => clienttranslate('No specific rules'),
      'bibliography' => clienttranslate('Official Campaign Book #1 - Battle for Normandy Scenario #9 - Official Scenario #4088'),
    ],
  ],
  'equipment_packs' => [
  ],
];