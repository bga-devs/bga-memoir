<?php
namespace M44\Scenarios;

$scenarios[4232] = [
  'meta_data' => [
    'scenario_id' => '4232',
    'status' => 'APPROVED',
    'can_translate' => 'true',
    'mod_date' => '2010-06-04 15:10:48',
    'ownerID' => 8860,
    'software' => 'sed 1.2',
    'create_by' => 8860,
    'create_date' => '2008-10-22 00:16:33',
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
    'cards_player2' => 5,
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
        'row' => 0,
        'col' => 4,
        'unit' => [
          'name' => 'gunger',
        ],
      ],
      1 => [
        'row' => 0,
        'col' => 6,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      2 => [
        'row' => 0,
        'col' => 16,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      3 => [
        'row' => 0,
        'col' => 20,
        'terrain' => [
          'name' => 'droad',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'gunger',
        ],
        'tags' => [
          0 => [
            'name' => 'medal4',
            'group' => [],
            'medal' => [
              'permanent' => false,
              'counts_for' => 1,
              'nbr_hex' => 1,
            ],
          ],
        ],
      ],
      4 => [
        'row' => 1,
        'col' => 9,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      5 => [
        'row' => 1,
        'col' => 11,
        'terrain' => [
          'name' => 'dhill',
        ],
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      6 => [
        'row' => 1,
        'col' => 13,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      7 => [
        'row' => 1,
        'col' => 15,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      8 => [
        'row' => 1,
        'col' => 17,
        'unit' => [
          'name' => 'infger',
          'badge' => 'badge37',
        ],
      ],
      9 => [
        'row' => 1,
        'col' => 19,
        'terrain' => [
          'name' => 'droad',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      10 => [
        'row' => 1,
        'col' => 21,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      11 => [
        'row' => 2,
        'col' => 4,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      12 => [
        'row' => 2,
        'col' => 6,
        'terrain' => [
          'name' => 'dhill',
        ],
        'unit' => [
          'name' => 'infger',
          'badge' => 'badge37',
        ],
      ],
      13 => [
        'row' => 2,
        'col' => 12,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      14 => [
        'row' => 2,
        'col' => 18,
        'terrain' => [
          'name' => 'droad',
          'orientation' => 2,
        ],
      ],
      15 => [
        'row' => 3,
        'col' => 3,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      16 => [
        'row' => 3,
        'col' => 17,
        'terrain' => [
          'name' => 'droad',
          'orientation' => 2,
        ],
      ],
      17 => [
        'row' => 4,
        'col' => 2,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      18 => [
        'row' => 4,
        'col' => 14,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      19 => [
        'row' => 4,
        'col' => 16,
        'terrain' => [
          'name' => 'droad',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      20 => [
        'row' => 4,
        'col' => 22,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      21 => [
        'row' => 5,
        'col' => 3,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      22 => [
        'row' => 5,
        'col' => 15,
        'terrain' => [
          'name' => 'droad',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      23 => [
        'row' => 5,
        'col' => 17,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      24 => [
        'row' => 5,
        'col' => 21,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      25 => [
        'row' => 6,
        'col' => 6,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      26 => [
        'row' => 6,
        'col' => 8,
        'terrain' => [
          'name' => 'droadcurve',
          'orientation' => 6,
        ],
        'unit' => [
          'name' => 'infbrit',
          'badge' => 'badge37',
        ],
      ],
      27 => [
        'row' => 6,
        'col' => 10,
        'terrain' => [
          'name' => 'droad',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      28 => [
        'row' => 6,
        'col' => 12,
        'terrain' => [
          'name' => 'droad',
          'orientation' => 1,
        ],
      ],
      29 => [
        'row' => 6,
        'col' => 14,
        'terrain' => [
          'name' => 'droadcurve',
          'orientation' => 3,
        ],
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      30 => [
        'row' => 7,
        'col' => 5,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      31 => [
        'row' => 7,
        'col' => 7,
        'terrain' => [
          'name' => 'droad',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      32 => [
        'row' => 7,
        'col' => 15,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      33 => [
        'row' => 8,
        'col' => 6,
        'terrain' => [
          'name' => 'droad',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'tankbrit',
        ],
        'tags' => [
          0 => [
            'name' => 'medal2',
            'group' => [],
            'medal' => [
              'permanent' => false,
              'counts_for' => 1,
              'nbr_hex' => 1,
            ],
          ],
        ],
      ],
      34 => [
        'row' => 8,
        'col' => 14,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
    ],
    'labels' => [
      0 => [
        'row' => 6,
        'col' => 14,
        'text' => [
          0 => clienttranslate('22nd Armoured Brigade'),
        ],
      ],
    ],
  ],
  'text' => [
    'en' => [
      'name' => clienttranslate('1st Armoured to the rescue'),
      'historical' => clienttranslate('By late morning, his left flank overrun by the coordinated mass of German Panzers, General Neil Ritchie, commander of the Eighth Army, ordered two armored brigades of Norrie\'s 1st Armoured Division south to the rescue.

Moving in column, the 22nd Armoured Brigade soon collided with the German Panzers, already frontally deployed in battle formation. Surprised before they could spread out, the hapless British tank commanders were quickly pounded into the sand. Their remnants fled north to join the only intact British armoured brigade.

The stage is set, the battle lines drawn, and you are in command. The rest is history.'),
      'description' => clienttranslate('Axis Player
[Germany]
Take 6 Command cards.
You move first.

Allied Player
[Great Britain]
Take 5 Command cards.
'),
      'victory' => clienttranslate('5 Medals.

The Road hexes on the opposite edges of the board are Temporary Medal Objectives for the Allied and Axis forces respectively.
'),
      'rules' => clienttranslate('North African Desert Rules are in effect (Actions 9 - North African Desert Rules). In addition, Allied Armor units may only move 2 hexes and battle, not the normal 3 hexes.

British Commonwealth Forces command rules are in effect.

Special Weapon Asset rules are in effect for the units equipped with Anti-Tank weapons.'),
      'bibliography' => clienttranslate('Scenario 6 -British Desert Expansion'),
    ],
  ],
];
