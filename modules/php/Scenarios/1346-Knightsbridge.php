<?php
namespace M44\Scenarios;

$scenarios[1346] = [
  'meta_data' => [
    'scenario_id' => '1346',
    'status' => 'APPROVED',
    'can_translate' => 'false',
    'mod_date' => '2010-05-27 06:41:58',
    'ownerID' => 8860,
    'software' => 'sed 1.2',
    'create_by' => 8860,
    'create_date' => '2008-04-07 17:58:38',
    'mod_by' => 291520,
    'author_id' => 8860,
  ],
  'game_info' => [
    'front' => 'MEDITERRANEAN',
    'date_begin' => '1942-06-12',
    'date_end' => '1942-06-12',
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
    'options' => ['north_african_desert_rules' => true],
  ],
  'board' => [
    'type' => 'STANDARD',
    'face' => 'DESERT',
    'hexagons' => [
      0 => [
        'row' => 0,
        'col' => 4,
        'unit' => [
          'name' => 'tank2ger',
          'nbr_units' => 4,
          'badge' => 'badge4',
        ],
      ],
      1 => [
        'row' => 0,
        'col' => 16,
        'terrain' => [
          'name' => 'oasis',
        ],
      ],
      2 => [
        'row' => 0,
        'col' => 20,
        'unit' => [
          'name' => 'tank2ger',
          'nbr_units' => 4,
          'badge' => 'badge4',
        ],
      ],
      3 => [
        'row' => 1,
        'col' => 3,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      4 => [
        'row' => 1,
        'col' => 5,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      5 => [
        'row' => 1,
        'col' => 7,
        'unit' => [
          'name' => 'gunger',
        ],
      ],
      6 => [
        'row' => 1,
        'col' => 11,
        'unit' => [
          'name' => 'gunger',
        ],
      ],
      7 => [
        'row' => 1,
        'col' => 13,
        'terrain' => [
          'name' => 'wadi',
          'orientation' => 3,
        ],
      ],
      8 => [
        'row' => 1,
        'col' => 17,
        'unit' => [
          'name' => 'gunger',
        ],
      ],
      9 => [
        'row' => 1,
        'col' => 19,
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
        'col' => 0,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      12 => [
        'row' => 2,
        'col' => 14,
        'terrain' => [
          'name' => 'wcurve',
          'orientation' => 4,
        ],
      ],
      13 => [
        'row' => 3,
        'col' => 1,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      14 => [
        'row' => 3,
        'col' => 13,
        'terrain' => [
          'name' => 'wadi',
          'orientation' => 2,
        ],
      ],
      15 => [
        'row' => 4,
        'col' => 2,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      16 => [
        'row' => 7,
        'col' => 1,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      17 => [
        'row' => 7,
        'col' => 3,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      18 => [
        'row' => 7,
        'col' => 7,
        'unit' => [
          'name' => 'gunus',
        ],
      ],
      19 => [
        'row' => 7,
        'col' => 17,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      20 => [
        'row' => 7,
        'col' => 19,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      21 => [
        'row' => 7,
        'col' => 21,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      22 => [
        'row' => 7,
        'col' => 23,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      23 => [
        'row' => 8,
        'col' => 2,
        'unit' => [
          'name' => 'tankus',
        ],
      ],
      24 => [
        'row' => 8,
        'col' => 10,
        'terrain' => [
          'name' => 'oasis',
        ],
      ],
      25 => [
        'row' => 8,
        'col' => 12,
        'terrain' => [
          'name' => 'bled',
        ],
      ],
      26 => [
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
          0 => clienttranslate('15th Panzer'),
        ],
      ],
      1 => [
        'row' => 0,
        'col' => 18,
        'text' => [
          0 => clienttranslate('21st Panzer'),
        ],
      ],
      2 => [
        'row' => 8,
        'col' => 4,
        'text' => [
          0 => clienttranslate('4th Armor'),
        ],
      ],
      3 => [
        'row' => 8,
        'col' => 20,
        'text' => [
          0 => clienttranslate('2nd Armor'),
        ],
      ],
    ],
  ],
  'text' => [
    'en' => [
      'name' => clienttranslate('Knightsbridge'),
      'subtitle' => clienttranslate('Battle of Gazala'),
      'historical' => clienttranslate('The Battle of Gazala was a series of clashes between Rommel and the British in the late spring of 1942 near the Libyan coast. The brilliant, but risky maneuvers by the \'Desert Fox\' in late May and early June were a success, but left his armored units low on fuel and widely scattered. A major attack by British reserves might very well have delivered a decisive victory and destroyed most of Rommel\'s mobile units, but a failure to exploit this weakness by the British allowed him time to re-supply.

On June 12th, with reports of a gap in the German tank formations, British high command had assembled the 2nd and 4th Tank brigades for an attack. Although the British had a numerical advantage, Rommel used his superior leadership and equipment to counter-attack. He ordered a frontal attack by the 15th Panzer, while the 21st Panzer attempted an outflanking move. The British forces, after a fierce engagement around Knightsbridge, were destroyed in this climactic battle.

The stage is set, the battle lines are drawn, and you are in command. The rest is history.

'),
      'description' => clienttranslate('Axis Player: Take 6 Command cards
You move first.

Allied Player: Take 4 Command cards.'),
      'victory' => clienttranslate('5 Medals'),
      'rules' => clienttranslate('The Axis Special Forces tank units have 4 figures. Place a Special Force token in the same hex with these units to distinguish them from the other units.

Armor movement is amended as follows:
An ordered Axis Armor unit may move up to 3 hexes and battle.
An ordered Allied Armor unit may move up to 2 hexes and battle.

North Africa Desert rules are in effect (see p. 3).'),
    ],
  ],
];
