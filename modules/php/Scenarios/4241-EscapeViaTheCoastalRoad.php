<?php
namespace M44\Scenarios;

$scenarios[4241] = [
  'meta_data' => [
    'number' => 'MT 08',
    'can_translate' => 'true',
    'software' => 'sed 1.2',
    'scenario_id' => '4241',
    'create_by' => 8860,
    'create_date' => '2008-10-22 00:17:27',
    'mod_by' => 14,
    'mod_date' => '2008-10-22 00:17:27',
    'ownerID' => 8860,
    'author_id' => 8860,
    'status' => 'APPROVED',
  ],
  'game_info' => [
    'front' => 'MEDITERRANEAN',
    'operationID' => '37',
    'date_begin' => '1942-06-14',
    'date_end' => '1942-06-14',
    'type' => 'HISTORICAL',
    'starting' => 'PLAYER2',
    'side_player1' => 'AXIS',
    'side_player2' => 'ALLIES',
    'country_player1' => 'DE',
    'cards_player1' => 5,
    'cards_player2' => 5,
    'victory_player1' => 6,
    'victory_player2' => 6,
    'options' => [
      'north_african_desert_rules' => true,
      'italy_royal_army' => true,
      'partial_blitz_rules' => 'all',
      'british_commonwealth' => true,
    ],
  ],
  'board' => [
    'type' => 'STANDARD',
    'face' => 'DESERT',
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
        'col' => 2,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      2 => [
        'row' => 0,
        'col' => 4,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      3 => [
        'row' => 0,
        'col' => 6,
        'unit' => [
          'name' => 'tankger',
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
        'col' => 12,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      6 => [
        'row' => 0,
        'col' => 14,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      7 => [
        'row' => 0,
        'col' => 16,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      8 => [
        'row' => 0,
        'col' => 18,
        'unit' => [
          'name' => 'infger',
        ],
      ],
      9 => [
        'row' => 3,
        'col' => 1,
        'terrain' => [
          'name' => 'descarpment',
        ],
      ],
      10 => [
        'row' => 3,
        'col' => 3,
        'terrain' => [
          'name' => 'descarpment',
        ],
      ],
      11 => [
        'row' => 3,
        'col' => 7,
        'terrain' => [
          'name' => 'descarpment',
        ],
      ],
      12 => [
        'row' => 3,
        'col' => 9,
        'terrain' => [
          'name' => 'descarpment',
        ],
      ],
      13 => [
        'row' => 3,
        'col' => 11,
        'terrain' => [
          'name' => 'descarpment',
        ],
      ],
      14 => [
        'row' => 3,
        'col' => 17,
        'terrain' => [
          'name' => 'descarpment',
        ],
      ],
      15 => [
        'row' => 3,
        'col' => 19,
        'terrain' => [
          'name' => 'descarpment',
        ],
      ],
      16 => [
        'row' => 3,
        'col' => 21,
        'terrain' => [
          'name' => 'descarpment',
        ],
      ],
      17 => [
        'row' => 4,
        'col' => 20,
        'terrain' => [
          'name' => 'droadcurve',
          'orientation' => 6,
        ],
      ],
      18 => [
        'row' => 4,
        'col' => 22,
        'terrain' => [
          'name' => 'droad',
          'orientation' => 1,
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
          'badge' => 'badge38',
        ],
      ],
      19 => [
        'row' => 4,
        'col' => 24,
        'terrain' => [
          'name' => 'droad',
          'orientation' => 1,
        ],
        'tags' => [
          0 => [
            'name' => 'tag4',
            'behavior' => 'EXIT_MARKER',
            'group' => [],
            'side' => \ALLIES,
          ],
        ],
      ],
      20 => [
        'row' => 5,
        'col' => 5,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      21 => [
        'row' => 5,
        'col' => 9,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      22 => [
        'row' => 5,
        'col' => 11,
        'unit' => [
          'name' => 'infbrit',
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
        'col' => 19,
        'terrain' => [
          'name' => 'droad',
          'orientation' => 2,
        ],
      ],
      25 => [
        'row' => 5,
        'col' => 23,
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
          'badge' => 'badge38',
        ],
      ],
      26 => [
        'row' => 6,
        'col' => 4,
        'terrain' => [
          'name' => 'droadcurve',
          'orientation' => 6,
        ],
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      27 => [
        'row' => 6,
        'col' => 6,
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
        'col' => 8,
        'terrain' => [
          'name' => 'droad',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      29 => [
        'row' => 6,
        'col' => 10,
        'terrain' => [
          'name' => 'droad',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'gunbrit',
        ],
      ],
      30 => [
        'row' => 6,
        'col' => 12,
        'terrain' => [
          'name' => 'droad',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      31 => [
        'row' => 6,
        'col' => 14,
        'terrain' => [
          'name' => 'droad',
          'orientation' => 1,
        ],
      ],
      32 => [
        'row' => 6,
        'col' => 16,
        'terrain' => [
          'name' => 'droad',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      33 => [
        'row' => 6,
        'col' => 18,
        'terrain' => [
          'name' => 'droadcurve',
          'orientation' => 3,
        ],
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      34 => [
        'row' => 7,
        'col' => 3,
        'terrain' => [
          'name' => 'droad',
          'orientation' => 2,
        ],
      ],
      35 => [
        'row' => 7,
        'col' => 7,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      36 => [
        'row' => 7,
        'col' => 19,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      37 => [
        'row' => 8,
        'col' => 0,
        'terrain' => [
          'name' => 'droad',
          'orientation' => 1,
        ],
        'tags' => [
          0 => [
            'name' => 'tag4',
            'behavior' => 'EXIT_MARKER',
            'group' => [],
            'side' => \ALLIES,
          ],
        ],
      ],
      38 => [
        'row' => 8,
        'col' => 2,
        'terrain' => [
          'name' => 'droadcurve',
          'orientation' => 3,
        ],
      ],
      39 => [
        'row' => 8,
        'col' => 8,
        'terrain' => [
          'name' => 'coastcurve',
          'orientation' => 6,
        ],
      ],
      40 => [
        'row' => 8,
        'col' => 10,
        'terrain' => [
          'name' => 'coast',
          'orientation' => 1,
        ],
      ],
      41 => [
        'row' => 8,
        'col' => 12,
        'terrain' => [
          'name' => 'coast',
          'orientation' => 1,
        ],
      ],
      42 => [
        'row' => 8,
        'col' => 14,
        'terrain' => [
          'name' => 'coast',
          'orientation' => 1,
        ],
      ],
      43 => [
        'row' => 8,
        'col' => 16,
        'terrain' => [
          'name' => 'coast',
          'orientation' => 1,
        ],
      ],
      44 => [
        'row' => 8,
        'col' => 18,
        'terrain' => [
          'name' => 'coast',
          'orientation' => 1,
        ],
      ],
      45 => [
        'row' => 8,
        'col' => 20,
        'terrain' => [
          'name' => 'coast',
          'orientation' => 1,
        ],
      ],
      46 => [
        'row' => 8,
        'col' => 22,
        'terrain' => [
          'name' => 'coast',
          'orientation' => 1,
        ],
      ],
      47 => [
        'row' => 8,
        'col' => 24,
        'terrain' => [
          'name' => 'coast',
          'orientation' => 1,
        ],
      ],
    ],
    'labels' => [
      0 => [
        'row' => 4,
        'col' => 22,
        'text' => [
          0 => clienttranslate('Brescia and Pavia Divisions'),
        ],
      ],
      1 => [
        'row' => 6,
        'col' => 14,
        'text' => [
          0 => clienttranslate('50th (Northumbrian) Infantry Division'),
        ],
      ],
    ],
  ],
  'text' => [
    'en' => [
      'name' => clienttranslate('Escape via the Coastal Road'),
      'sdate' => clienttranslate('June 14, 1942'),
      'historical' => clienttranslate('Building up their forces through the now opened supply lines, Rommel\'s Afrikakorps eventually broke out of the cauldron, seizing Bir Hakeim but only capturing about 500 wounded Frenchmen . On June 13 "Black Sunday" , 21st Panzer shreds the 22nd Armoured Brigade to pieces, threatening Tobruk and cutting off XIII Corps on the Gazala line. The next day, Auchinlek authorizes General Ritchie to withdraw.

Defenders in El Adem and neighbouring boxes held firm, allowing the 1st South African Division to escape intact along the coastal road. But the road could not accommodate two divisions. With Panzer Divisions blocking the east, the remaining brigades of the Northumbrian Division were forced to attack the Brescia and Pavia Divisions and head south in the desert, before turning back west. The German Panzers raced north, but could not move fast enough to close the road before the bulk of British troops had escaped!

The stage is set, the battle lines drawn, and you are in command. The rest is history.'),
      'description' => clienttranslate('Axis Player
[Germany/Italy]
Take 5 Command cards.

Allied Player
[Great Britain]
Take 5 Command cards.
You move first.
'),
      'rules' => clienttranslate('North African Desert Rules are in effect (Actions 9 - North African Desert Rules). In addition, Allied Armor units may only move 2 hexes and battle, not the normal 3 hexes.

British Commonwealth Forces and Italian Royal Army command rules are in effect.

Air rules are not in effect. The Air Sortie cards are set aside and are not used in this mission.
'),
      'victory' => clienttranslate('6 Medals.

Exit markers are in effect on the two road hexes at the board\'s edges, for the Allied forces.
'),
      'bibliography' => clienttranslate('Scenario 8 - British Desert Expansion
'),
      'mod_date' => clienttranslate('2008-10-22T00:17:27'),
      'ownerID' => clienttranslate('8860'),
    ],
  ],
];
