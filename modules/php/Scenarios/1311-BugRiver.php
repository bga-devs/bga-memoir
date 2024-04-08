<?php
namespace M44\Scenarios;

$scenarios[1311] = [
  'meta_data' => [
    'scenario_id' => '1311',
    'status' => 'APPROVED',
    'can_translate' => 'false',
    'mod_date' => '2010-05-31 07:04:41',
    'ownerID' => 8860,
    'software' => 'sed 1.2',
    'create_by' => 8860,
    'create_date' => '2008-04-07 17:39:28',
    'mod_by' => 291520,
    'author_id' => 8860,
  ],
  'game_info' => [
    'front' => 'EASTERN',
    'operationID' => '25',
    'date_begin' => '1941-06-22',
    'date_end' => '1941-06-22',
    'type' => 'HISTORICAL',
    'starting' => 'PLAYER1',
    'side_player1' => 'AXIS',
    'side_player2' => 'ALLIES',
    'country_player1' => 'DE',
    'country_player2' => 'RU',
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
      'russian_commissar_rule' => 'ALLIES',
      'blitz_rules' => true,
      'supply_train_rules' => [
        'side' => 'ALLIES',
        'nbr_units' => [1, 1], // nbr units for each [loco, wagon]
        'unit' => [
          'name' => 'infru',
          'behavior' => 'CANNOT_BE_ACTIVATED_TILL_TURN',
          'turn' => 1
        ],
      ],
    ],
  ],
  'board' => [
    'type' => 'STANDARD',
    'face' => 'COUNTRY',
    'hexagons' => [
      0 => [
        'row' => 0,
        'col' => 0,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 3,
        ],
      ],
      1 => [
        'row' => 0,
        'col' => 2,
        'unit' => [
          'name' => 'gunger',
        ],
      ],
      2 => [
        'row' => 0,
        'col' => 4,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      3 => [
        'row' => 0,
        'col' => 6,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 2,
        ],
      ],
      4 => [
        'row' => 0,
        'col' => 8,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 3,
        ],
      ],
      5 => [
        'row' => 0,
        'col' => 10,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      6 => [
        'row' => 0,
        'col' => 12,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      7 => [
        'row' => 0,
        'col' => 14,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 2,
        ],
      ],
      8 => [
        'row' => 0,
        'col' => 16,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 3,
        ],
      ],
      9 => [
        'row' => 0,
        'col' => 20,
        'unit' => [
          'name' => 'gunger',
        ],
      ],
      10 => [
        'row' => 0,
        'col' => 22,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      11 => [
        'row' => 1,
        'col' => 3,
        'unit' => [
          'name' => 'infger',
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
        'col' => 9,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      14 => [
        'row' => 1,
        'col' => 11,
        'terrain' => [
          'name' => 'woods',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      15 => [
        'row' => 1,
        'col' => 13,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      16 => [
        'row' => 1,
        'col' => 21,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      17 => [
        'row' => 1,
        'col' => 23,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      18 => [
        'row' => 2,
        'col' => 0,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      19 => [
        'row' => 2,
        'col' => 2,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      20 => [
        'row' => 2,
        'col' => 18,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      21 => [
        'row' => 3,
        'col' => 5,
        'rect_terrain' => [
          'name' => 'casemate',
        ],
        'unit' => [
          'name' => 'tankru',
        ],
      ],
      22 => [
        'row' => 3,
        'col' => 15,
        'rect_terrain' => [
          'name' => 'casemate',
        ],
        'unit' => [
          'name' => 'tankru',
        ],
      ],
      23 => [
        'row' => 3,
        'col' => 19,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      24 => [
        'row' => 3,
        'col' => 21,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      25 => [
        'row' => 3,
        'col' => 23,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      26 => [
        'row' => 4,
        'col' => 2,
        'terrain' => [
          'name' => 'woods',
        ],
        'unit' => [
          'name' => 'infru',
        ],
      ],
      27 => [
        'row' => 4,
        'col' => 8,
        'terrain' => [
          'name' => 'buildings',
        ],
        'unit' => [
          'name' => 'infru',
        ],
      ],
      28 => [
        'row' => 4,
        'col' => 12,
        'terrain' => [
          'name' => 'buildings',
        ],
      ],
      29 => [
        'row' => 4,
        'col' => 18,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      30 => [
        'row' => 4,
        'col' => 20,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      31 => [
        'row' => 5,
        'col' => 1,
        'terrain' => [
          'name' => 'rail',
          'orientation' => 1,
        ],
      ],
      32 => [
        'row' => 5,
        'col' => 3,
        'terrain' => [
          'name' => 'rail',
          'orientation' => 1,
        ],
      ],
      33 => [
        'row' => 5,
        'col' => 5,
        'terrain' => [
          'name' => 'rail',
          'orientation' => 1,
        ],
      ],
      34 => [
        'row' => 5,
        'col' => 7,
        'terrain' => [
          'name' => 'rail',
          'orientation' => 1,
        ],
      ],
      35 => [
        'row' => 5,
        'col' => 9,
        'terrain' => [
          'name' => 'station',
          'orientation' => 1,
        ],
      ],
      36 => [
        'row' => 5,
        'col' => 11,
        'terrain' => [
          'name' => 'rail',
          'orientation' => 1,
        ],
      ],
      37 => [
        'row' => 5,
        'col' => 13,
        'terrain' => [
          'name' => 'rail',
          'orientation' => 1,
        ],
      ],
      38 => [
        'row' => 5,
        'col' => 15,
        'terrain' => [
          'name' => 'railcurve',
          'orientation' => 5,
        ],
      ],
      39 => [
        'row' => 6,
        'col' => 6,
        'unit' => [
          'name' => 'infru',
        ],
      ],
      40 => [
        'row' => 6,
        'col' => 8,
        'terrain' => [
          'name' => 'buildings',
        ],
      ],
      41 => [
        'row' => 6,
        'col' => 16,
        'terrain' => [
          'name' => 'railcurve',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'infru',
        ],
      ],
      42 => [
        'row' => 6,
        'col' => 18,
        'terrain' => [
          'name' => 'rail',
          'orientation' => 1,
        ],
      ],
      43 => [
        'row' => 6,
        'col' => 20,
        'terrain' => [
          'name' => 'rail',
          'orientation' => 1,
        ],
      ],
      44 => [
        'row' => 6,
        'col' => 22,
        'terrain' => [
          'name' => 'rail',
          'orientation' => 1,
        ],
       'rect_terrain' => [
          'name' => 'loco',
          'orientation' => 1,
        ],
      ],
      45 => [
        'row' => 6,
        'col' => 24,
        'terrain' => [
          'name' => 'rail',
          'orientation' => 1,
        ],
        'rect_terrain' => [
          'name' => 'wagon',
          'orientation' => 1,
        ],
      ],
      46 => [
        'row' => 7,
        'col' => 1,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      47 => [
        'row' => 7,
        'col' => 3,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      48 => [
        'row' => 7,
        'col' => 5,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      49 => [
        'row' => 7,
        'col' => 7,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      50 => [
        'row' => 7,
        'col' => 9,
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
            'name' => 'medal2',
          ],
        ],
      ],
      51 => [
        'row' => 7,
        'col' => 11,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      52 => [
        'row' => 7,
        'col' => 13,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      53 => [
        'row' => 7,
        'col' => 15,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 5,
        ],
      ],
      54 => [
        'row' => 7,
        'col' => 17,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      55 => [
        'row' => 7,
        'col' => 19,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      56 => [
        'row' => 7,
        'col' => 21,
        'unit' => [
          'name' => 'infru',
        ],
      ],
      57 => [
        'row' => 8,
        'col' => 6,
        'unit' => [
          'name' => 'tankru',
        ],
      ],
      58 => [
        'row' => 8,
        'col' => 16,
        'terrain' => [
          'name' => 'river',
          'orientation' => 3,
        ],
      ],
    ],
    'labels' => [
      0 => [
        'row' => 0,
        'col' => 8,
        'text' => [
          0 => clienttranslate('River Bug'),
        ],
      ],
      1 => [
        'row' => 0,
        'col' => 16,
        'text' => [
          0 => clienttranslate('River Bug'),
        ],
      ],
      2 => [
        'row' => 2,
        'col' => 6,
        'text' => [
          0 => clienttranslate('Field Bunker'),
        ],
      ],
      3 => [
        'row' => 2,
        'col' => 14,
        'text' => [
          0 => clienttranslate('Field Bunker'),
        ],
      ],
      4 => [
        'row' => 4,
        'col' => 12,
        'text' => [
          0 => clienttranslate('Matykaky'),
        ],
      ],
      5 => [
        'row' => 6,
        'col' => 4,
        'text' => [
          0 => clienttranslate('River Jasnaja'),
        ],
      ],
      6 => [
        'row' => 6,
        'col' => 22,
        'text' => [
          0 => clienttranslate('Supply Train'),
        ],
      ],
    ],
  ],
  'text' => [
    'en' => [
      'name' => clienttranslate('Bug River'),
      'subtitle' => clienttranslate('Unternehmen Barbarossa'),
      'historical' => clienttranslate('On June 22, 1941, the 18th Panzer Division made an unusual assault across the Bug River. The first wave of infantry in Sturmboot assault craft was followed by anti-tank and artillery on decked inflatable rafts. Even more remarkable, were the following 80 tanks, originally waterproofed for operation Sealion, that slowly and successfully transversed the riverbed of the Bug.

The Russians defending the area were the 62nd Fortified District. They had only limited support but had manned some prepared positions, including dug-in tank turrets.

After the initial German Blitz, the infantry and tanks made quick work of the Russian defenders.

The stage is set, the battle lines are drawn, and you are in command. The rest is history.

'),
      'description' => clienttranslate('Axis Player: Take 6 Command cards
You move first.

Russian Player: Take 4 Command cards.


Note: this bonus scenario requires both the Terrain Pack and the Eastern Front expansions to be played.'),
      'victory' => clienttranslate('5  Medals

An Axis unit that captures the bridge counts as one Victory Medal. Place an Objective Medal on the Bridge hex. As long as the Axis unit remains on the hex, it continues to count toward the Axis victory. If the unit moves off or is eliminated, it no longer counts.
'),
      'rules' => clienttranslate('Blitz Rules are in effect (p.4).

Russian Command rules are in effect (see p.3).

The bunkers are Field Bunkers (p.5).

The Train is a Supply Train. Refer to p.12 of the Terrain Pack rules. The Supply Train, locomotive and car, are each loaded with one Infantry unit. '),
    ],
  ],
];
