<?php
namespace M44\Scenarios;

$scenarios[4089] = [
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
    'mod_date' => '2024-11-01 22:14:03',
    'mod_by' => 1918554,
    'id' => 25574,
    'number' => NULL,
    'can_translate' => false,
    'scenario_id' => 4089,
  ],
  'game_info' => [
    'front' => 'western',
    'type' => 'historical',
    'starting' => 'PLAYER2',
    'side_player1' => 'AXIS',
    'side_player2' => 'ALLIES',
    'country_player1' => 'DE',
    'country_player2' => 'US',
    'cards_player1' => 5,
    'cards_player2' => 4,
    'victory_player1' => 5,
    'victory_player2' => 5,
    'operation' => NULL,
    'expert' => false,
    'date_begin' => '1944-07-09',
    'date_end' => '1944-07-10',
    'operationID' => 12,
    'options' => [
      'night_visibility_reverse_rule' => true,
      'night_visibility_team_turn' => AXIS,
    ],
  ],
  'board' => [
    'type' => 'standard',
    'face' => 'country',
    'hexagons' => [
      0 => [
        'col' => 10,
        'row' => 0,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 2,
        ],
      ],
      1 => [
        'col' => 14,
        'row' => 0,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 3,
        ],
      ],
      2 => [
        'col' => 12,
        'row' => 0,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
        'rect_terrain' => [
          'name' => 'bridge',
          'orientation' => 1,
        ],
        'tags' => [
          0 => [
            'name' => 'tag4',
            'behavior' => 'EXIT_MARKER',
            'group' => [],
            'side' => \AXIS,
          ],
        ],
      ],
      3 => [
        'col' => 2,
        'row' => 0,
        'terrain' => [
          'name' => 'woods',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      4 => [
        'col' => 3,
        'row' => 1,
        'terrain' => [
          'name' => 'woods',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      5 => [
        'col' => 7,
        'row' => 1,
        'terrain' => [
          'name' => 'woods',
        ],
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      6 => [
        'col' => 8,
        'row' => 2,
        'terrain' => [
          'name' => 'buildings',
        ],
      ],
      7 => [
        'col' => 11,
        'row' => 3,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      8 => [
        'col' => 13,
        'row' => 3,
        'terrain' => [
          'name' => 'buildings',
        ],
      ],
      9 => [
        'col' => 16,
        'row' => 2,
        'terrain' => [
          'name' => 'buildings',
        ],
      ],
      10 => [
        'col' => 17,
        'row' => 1,
        'terrain' => [
          'name' => 'buildings',
        ],
      ],
      11 => [
        'col' => 16,
        'row' => 4,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      12 => [
        'col' => 19,
        'row' => 5,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'infus',
        ],
      ],
      13 => [
        'col' => 14,
        'row' => 6,
      ],
      14 => [
        'col' => 21,
        'row' => 3,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      15 => [
        'col' => 23,
        'row' => 3,
        'terrain' => [
          'name' => 'woods',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      16 => [
        'col' => 24,
        'row' => 2,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      17 => [
        'col' => 21,
        'row' => 1,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      18 => [
        'col' => 23,
        'row' => 5,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'infus',
        ],
      ],
      19 => [
        'col' => 11,
        'row' => 5,
      ],
      20 => [
        'col' => 13,
        'row' => 5,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      21 => [
        'col' => 12,
        'row' => 6,
        'terrain' => [
          'name' => 'woods',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      22 => [
        'col' => 8,
        'row' => 4,
        'terrain' => [
          'name' => 'woods',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      23 => [
        'col' => 5,
        'row' => 3,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      24 => [
        'col' => 1,
        'row' => 3,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      25 => [
        'col' => 2,
        'row' => 4,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      26 => [
        'col' => 3,
        'row' => 5,
        'terrain' => [
          'name' => 'hills',
        ],
        'unit' => [
          'name' => 'infus',
        ],
      ],
      27 => [
        'col' => 6,
        'row' => 6,
        'terrain' => [
          'name' => 'hills',
        ],
        'unit' => [
          'name' => 'infus',
        ],
      ],
      28 => [
        'col' => 23,
        'row' => 1,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      29 => [
        'col' => 24,
        'row' => 6,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      30 => [
        'col' => 22,
        'row' => 8,
        'unit' => [
          'name' => 'gunus',
        ],
      ],
      31 => [
        'col' => 18,
        'row' => 6,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      32 => [
        'col' => 17,
        'row' => 7,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      33 => [
        'col' => 13,
        'row' => 7,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      34 => [
        'col' => 11,
        'row' => 7,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      35 => [
        'col' => 7,
        'row' => 7,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      36 => [
        'col' => 1,
        'row' => 7,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      37 => [
        'col' => 0,
        'row' => 4,
        'unit' => [
          'name' => 'infus',
        ],
      ],
      38 => [
        'col' => 12,
        'row' => 2,
      ],
    ],
    'labels' => [
      0 => [
        'col' => 14,
        'row' => 0,
        'text' => [
          0 => 'Orne River',
        ],
      ],
      1 => [
        'col' => 12,
        'row' => 2,
        'text' => [
          0 => 'Caen',
        ],
      ],
    ],
    'groups' => [
    ],
  ],
  'packs' => [
    'air' => 1,
  ],
  'text' => [
    'en' => [
      'name' => clienttranslate('NIGHT WITHDRAWAL'),
      'sdate' => '',
      'subtitle' => '',
      'historical' => clienttranslate('Battle of Normandy Campaign - Taking Caen (Operation Charnwood):

Charnwood was the operational codename given to the mission of taking Caen, the elusive D-Day objective that the Germans still held on July 7. The Allied plan progressed at a slower pace than expected, but was forcing the German command to rethink their hold on the city. Rommel, the commander of Army Group B, ordered that all equipment be withdrawn across the Orne River to make a stand in Caen on the south bank of the river and allowing Allied troops to occupy all of Caen on the north bank. On the night of July 8-9, Axis forces began the withdrawal. They left behind rearguards to prevent the Allies from advancing too fast and managed to establish new defensive positions on the far side of the Orne.

The stage is set, the battle lines drawn, and you are in command. The rest is history.'),
      'description' => clienttranslate('Axis Player [Germany]: Take 5 Command cards.

Allied Player [Canada]: Take 4 Command cards. You move first.'),
      'victory' => clienttranslate('5 Medals. 

The hex with an Exit marker on the Axis player\'s baseline is an Exit hex for the Axis forces (not for the Allied forces)!'),
      'rules' => clienttranslate('Night Fall: The German player benefits from Night Attack conditions (Actions 19 - Night Attacks). Use the Night Visibility chart in reverse.

If you do not own the Pacific Theater or Air Pack expansion, use a 6-sided die. Place the 6 face up, to indicate an initial visibility of 6 hexes. At the start of each turn, the Axis player rolls 4 Battle dice to see if the visibility changes. For each star rolled, reduce the visibility range by 1, turning the 6 sided die on its appropriate side to reflect the reduced visibility. Once fully night, put the die aside and play the remainder of the scenario limited to close assault combat. 

'),
      'bibliography' => clienttranslate('Official Campaign Book #1 - Battle for Normandy Scenario #8 - Official Scenario #4089'),
    ],
  ],
  'equipment_packs' => [
  ],
];