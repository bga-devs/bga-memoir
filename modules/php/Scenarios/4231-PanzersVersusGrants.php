<?php
namespace M44\Scenarios;

$scenarios[4231] = [
  'meta_data' => [
    'scenario_id' => '4231',
    'status' => 'APPROVED',
    'can_translate' => 'true',
    'mod_date' => '2010-06-04 15:09:12',
    'ownerID' => 8860,
    'software' => 'sed 1.2',
    'create_by' => 8860,
    'create_date' => '2008-10-22 00:15:28',
    'mod_by' => 291520,
    'author_id' => 8860,
  ],
  'game_info' => [
    'front' => 'MEDITERRANEAN',
    'operationID' => '37',
    'date_begin' => '1942-05-27',
    'date_end' => '1942-05-27',
    'type' => 'HISTORICAL',
    'starting' => 'PLAYER1',
    'side_player1' => 'AXIS',
    'side_player2' => 'ALLIES',
    'country_player1' => 'DE',
    'cards_player1' => 6,
    'cards_player2' => 4,
    'victory_player1' => 5,
    'victory_player2' => 5,
    'victory_conditions' => [
      0 => [
        'standard' => [],
      ],
    ],
    'options' => [
      'north_african_desert_rules' => true,
      'partial_blitz_rules' => ALLIES,
      'british_commonwealth' => true,
    ],
  ],
  'board' => [
    'type' => 'STANDARD',
    'face' => 'DESERT',
    'hexagons' => [
      0 => [
        'row' => 1,
        'col' => 3,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      1 => [
        'row' => 1,
        'col' => 13,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      2 => [
        'row' => 2,
        'col' => 0,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      3 => [
        'row' => 2,
        'col' => 4,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      4 => [
        'row' => 2,
        'col' => 6,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      5 => [
        'row' => 2,
        'col' => 12,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      6 => [
        'row' => 2,
        'col' => 14,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      7 => [
        'row' => 2,
        'col' => 18,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      8 => [
        'row' => 2,
        'col' => 20,
        'terrain' => [
          'name' => 'palmtrees',
        ],
      ],
      9 => [
        'row' => 2,
        'col' => 22,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      10 => [
        'row' => 2,
        'col' => 24,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      11 => [
        'row' => 3,
        'col' => 1,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      12 => [
        'row' => 3,
        'col' => 21,
        'terrain' => [
          'name' => 'palmtrees',
        ],
      ],
      13 => [
        'row' => 3,
        'col' => 23,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      14 => [
        'row' => 4,
        'col' => 18,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      15 => [
        'row' => 4,
        'col' => 22,
        'terrain' => [
          'name' => 'palmtrees',
        ],
      ],
      16 => [
        'row' => 5,
        'col' => 19,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      17 => [
        'row' => 6,
        'col' => 14,
        'unit' => [
          'name' => 'tank2brit',
          'nbr_units' => 4,
          'badge' => 'badge24',
        ],
      ],
      18 => [
        'row' => 7,
        'col' => 1,
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      19 => [
        'row' => 7,
        'col' => 3,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      20 => [
        'row' => 7,
        'col' => 5,
        'unit' => [
          'name' => 'tank2brit',
          'nbr_units' => 4,
          'badge' => 'badge24',
        ],
      ],
      21 => [
        'row' => 7,
        'col' => 9,
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      22 => [
        'row' => 7,
        'col' => 17,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      23 => [
        'row' => 7,
        'col' => 21,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      24 => [
        'row' => 8,
        'col' => 8,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      25 => [
        'row' => 8,
        'col' => 10,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      26 => [
        'row' => 8,
        'col' => 14,
        'terrain' => [
          'name' => 'dcamp',
        ],
        'unit' => [
          'name' => 'infbrit',
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
      ],
      27 => [
        'row' => 8,
        'col' => 20,
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
    ],
    'labels' => [
      0 => [
        'row' => 2,
        'col' => 8,
        'text' => [
          0 => clienttranslate('15th Panzer'),
        ],
      ],
      1 => [
        'row' => 2,
        'col' => 22,
        'text' => [
          0 => clienttranslate('21st Panzer'),
        ],
      ],
      2 => [
        'row' => 7,
        'col' => 7,
        'text' => [
          0 => clienttranslate('4th Armoured Brigade'),
        ],
      ],
    ],
  ],
  'text' => [
    'en' => [
      'name' => clienttranslate('Panzers versus Grants'),
      'historical' => clienttranslate('The same day as the battle at Bir Hakeim, further on the right, 15th Panzer engaged the rest of the 7th Armoured Division. After sweeping over the 7th Motorized Brigade, the Panzers ran into heavy resistance from the Grants of 4th Armoured Brigade. German anti-tank support was slow in coming, and the range and power of the 75mm guns on the newly arrived Grant tanks soon made life aboard the German Mark IIIs hot and untenable.

Finally by late morning, 21st Panzer arrived on the left of the 15th, swinging past the action and hitting the British on the right. Most of the Grants were annihilated, forcing the rest to withdraw.

The stage is set, the battle lines drawn, and you are in command. The rest is history.'),
      'description' => clienttranslate('Axis Player
[Germany]
Take 6 Command cards.
You move first.

Allied Player
[Great Britain]
Take 4 Command cards.
'),
      'victory' => clienttranslate('5 Medals.

The HQ/Supply Tent is a Permanent Medal Objective for the German forces; the Medal is gained and the tent removed at the start of the Axis player\'s next turn.
'),
      'rules' => clienttranslate('North African Desert Rules are in effect (Actions 9 - North African Desert Rules). In addition, Allied Armor units may only move 2 hexes and battle, not the normal 3 hexes.

Capture HQ/Supply Tent rules are in effect (Action 17 - Capture HQ/Supply).

British Commonwealth Forces command rules are in effect.

Place badges on the British elite tank units (Troops 2 - Specialized Units).'),
      'bibliography' => clienttranslate('Scenario 5 - British Desert Expansion'),
    ],
  ],
];
