<?php
namespace M44\Scenarios;

$scenarios[3129] = [
  'meta_data' => [
    'scenario_id' => '3129',
    'status' => 'PRIVATE',
    'software' => 'sed 1.2',
  ],
  'game_info' => [
    'date_begin' => '1941-11-19',
    'front' => 'MEDITERRANEAN',
    'type' => 'HISTORICAL',
    'starting' => 'PLAYER2',
    'side_player1' => 'AXIS',
    'side_player2' => 'ALLIES',
    'country_player1' => 'DE',
    'country_player2' => 'US',
    'cards_player1' => 6,
    'cards_player2' => 6,
    'victory_player1' => 12,
    'victory_player2' => 12,
    'date_end' => '1941-12-05',
    'options' => [
      'north_african_desert_rules' => true,
      'british_commonwealth' => true,
      'italy_royal_army' => true,
    ],
  ],
  'board' => [
    'type' => 'BRKTHRU',
    'face' => 'DESERT',
    'hexagons' => [
      0 => [
        'row' => 0,
        'col' => 0,
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'gunbrit',
        ],
      ],
      1 => [
        'row' => 0,
        'col' => 2,
        'terrain' => [
          'name' => 'bled',
        ],
      ],
      2 => [
        'row' => 0,
        'col' => 4,
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      3 => [
        'row' => 0,
        'col' => 6,
        'obstacle' => [
          'name' => 'wire',
          'orientation' => 1,
        ],
      ],
      4 => [
        'row' => 0,
        'col' => 8,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'ALLIES',
          ],
        ],
      ],
      5 => [
        'row' => 0,
        'col' => 14,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      6 => [
        'row' => 0,
        'col' => 22,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      7 => [
        'row' => 1,
        'col' => 1,
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      8 => [
        'row' => 1,
        'col' => 3,
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      9 => [
        'row' => 1,
        'col' => 5,
        'obstacle' => [
          'name' => 'wire',
          'orientation' => 1,
        ],
      ],
      10 => [
        'row' => 1,
        'col' => 7,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'ALLIES',
          ],
        ],
      ],
      11 => [
        'row' => 1,
        'col' => 9,
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 2,
        ],
        'unit' => [
          'name' => 'infger',
          'badge' => 'badge38',
        ],
      ],
      12 => [
        'row' => 2,
        'col' => 0,
        'obstacle' => [
          'name' => 'wire',
          'orientation' => 1,
        ],
      ],
      13 => [
        'row' => 2,
        'col' => 2,
        'obstacle' => [
          'name' => 'wire',
          'orientation' => 1,
        ],
      ],
      14 => [
        'row' => 2,
        'col' => 4,
        'obstacle' => [
          'name' => 'wire',
          'orientation' => 1,
        ],
      ],
      15 => [
        'row' => 2,
        'col' => 6,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'ALLIES',
          ],
        ],
      ],
      16 => [
        'row' => 2,
        'col' => 14,
        'unit' => [
          'name' => 'inf2ger',
          'badge' => 'badge4',
        ],
      ],
      17 => [
        'row' => 2,
        'col' => 16,
        'terrain' => [
          'name' => 'bled',
        ],
        'unit' => [
          'name' => 'gunger',
          'badge' => 'badge5',
        ],
      ],
      18 => [
        'row' => 2,
        'col' => 18,
        'terrain' => [
          'name' => 'dcamp',
          'behavior' => 'OASIS_RECOVERY',
        ],
      ],
      19 => [
        'row' => 2,
        'col' => 20,
        'unit' => [
          'name' => 'tank2ger',
          'nbr_units' => '4',
          'badge' => 'badge10',
        ],
      ],
      20 => [
        'row' => 3,
        'col' => 1,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'ALLIES',
          ],
        ],
      ],
      21 => [
        'row' => 3,
        'col' => 3,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'ALLIES',
          ],
        ],
      ],
      22 => [
        'row' => 3,
        'col' => 5,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'ALLIES',
          ],
        ],
      ],
      23 => [
        'row' => 3,
        'col' => 13,
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      24 => [
        'row' => 3,
        'col' => 15,
        'unit' => [
          'name' => 'gunger',
          'badge' => 'badge35',
        ],
      ],
      25 => [
        'row' => 3,
        'col' => 17,
        'unit' => [
          'name' => 'tank2ger',
          'nbr_units' => '4',
          'badge' => 'badge10',
        ],
      ],
      26 => [
        'row' => 4,
        'col' => 6,
        'terrain' => [
          'name' => 'oasis',
          'behavior' => 'OASIS_RECOVERY',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      27 => [
        'row' => 5,
        'col' => 1,
        'terrain' => [
          'name' => 'bled',
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      28 => [
        'row' => 5,
        'col' => 23,
        'obstacle' => [
          'name' => 'wire',
          'orientation' => 1,
        ],
      ],
      29 => [
        'row' => 6,
        'col' => 0,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      30 => [
        'row' => 6,
        'col' => 2,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      31 => [
        'row' => 6,
        'col' => 4,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      32 => [
        'row' => 6,
        'col' => 6,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      33 => [
        'row' => 6,
        'col' => 8,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      34 => [
        'row' => 6,
        'col' => 14,
        'terrain' => [
          'name' => 'oasis',
          'behavior' => 'OASIS_RECOVERY',
        ],
      ],
      35 => [
        'row' => 6,
        'col' => 22,
        'obstacle' => [
          'name' => 'wire',
          'orientation' => 1,
        ],
      ],
      36 => [
        'row' => 6,
        'col' => 24,
        'terrain' => [
          'name' => 'bled',
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
      37 => [
        'row' => 7,
        'col' => 7,
        'terrain' => [
          'name' => 'bled',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      38 => [
        'row' => 7,
        'col' => 9,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      39 => [
        'row' => 7,
        'col' => 11,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      40 => [
        'row' => 7,
        'col' => 23,
        'obstacle' => [
          'name' => 'wire',
          'orientation' => 1,
        ],
      ],
      41 => [
        'row' => 8,
        'col' => 8,
        'terrain' => [
          'name' => 'dairfieldX',
          'orientation' => 1,
        ],
      ],
      42 => [
        'row' => 8,
        'col' => 10,
        'terrain' => [
          'name' => 'dairfield',
          'orientation' => 1,
        ],
      ],
      43 => [
        'row' => 8,
        'col' => 20,
        'terrain' => [
          'name' => 'bled',
        ],
      ],
      44 => [
        'row' => 10,
        'col' => 0,
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'tankger',
          'badge' => 'badge38',
        ],
      ],
      45 => [
        'row' => 10,
        'col' => 2,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      46 => [
        'row' => 11,
        'col' => 1,
        'terrain' => [
          'name' => 'oasis',
          'behavior' => 'OASIS_RECOVERY',
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
      47 => [
        'row' => 11,
        'col' => 3,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      48 => [
        'row' => 11,
        'col' => 11,
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      49 => [
        'row' => 11,
        'col' => 13,
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      50 => [
        'row' => 11,
        'col' => 21,
        'terrain' => [
          'name' => 'bled',
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
      51 => [
        'row' => 12,
        'col' => 0,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      52 => [
        'row' => 12,
        'col' => 2,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      53 => [
        'row' => 12,
        'col' => 8,
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      54 => [
        'row' => 12,
        'col' => 10,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      55 => [
        'row' => 12,
        'col' => 12,
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      56 => [
        'row' => 12,
        'col' => 24,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      57 => [
        'row' => 13,
        'col' => 7,
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      58 => [
        'row' => 13,
        'col' => 9,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      59 => [
        'row' => 13,
        'col' => 11,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      60 => [
        'row' => 13,
        'col' => 17,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      61 => [
        'row' => 13,
        'col' => 23,
        'terrain' => [
          'name' => 'dhill',
        ],
      ],
      62 => [
        'row' => 14,
        'col' => 6,
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      63 => [
        'row' => 14,
        'col' => 8,
        'unit' => [
          'name' => 'gunbrit',
          'badge' => 'badge35',
        ],
      ],
      64 => [
        'row' => 14,
        'col' => 10,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      65 => [
        'row' => 14,
        'col' => 16,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      66 => [
        'row' => 14,
        'col' => 18,
        'terrain' => [
          'name' => 'bled',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      67 => [
        'row' => 14,
        'col' => 22,
        'terrain' => [
          'name' => 'dhill',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'gunger',
          'badge' => 'badge46',
        ],
      ],
      68 => [
        'row' => 14,
        'col' => 24,
        'terrain' => [
          'name' => 'bled',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      69 => [
        'row' => 15,
        'col' => 5,
        'unit' => [
          'name' => 'tankbrit',
        ],
      ],
      70 => [
        'row' => 15,
        'col' => 7,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      71 => [
        'row' => 15,
        'col' => 9,
        'terrain' => [
          'name' => 'oasis',
          'behavior' => 'OASIS_RECOVERY',
        ],
      ],
      72 => [
        'row' => 15,
        'col' => 11,
        'unit' => [
          'name' => 'gunbrit',
          'badge' => 'badge5',
        ],
      ],
      73 => [
        'row' => 15,
        'col' => 17,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      74 => [
        'row' => 15,
        'col' => 19,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      75 => [
        'row' => 15,
        'col' => 21,
        'terrain' => [
          'name' => 'dhill',
        ],
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      76 => [
        'row' => 15,
        'col' => 23,
        'tags' => [
          0 => [
            'name' => 'tag3',
            'behavior' => 'MINE_FIELD',
            'side' => 'AXIS',
          ],
        ],
      ],
      77 => [
        'row' => 16,
        'col' => 8,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      78 => [
        'row' => 16,
        'col' => 10,
        'terrain' => [
          'name' => 'dcamp',
          'behavior' => 'OASIS_RECOVERY',
        ],
        'tags' => [
          0 => [
            'name' => 'medal2',
          ],
        ],
      ],
      79 => [
        'row' => 16,
        'col' => 12,
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
      80 => [
        'row' => 16,
        'col' => 14,
        'terrain' => [
          'name' => 'palmtrees',
        ],
        'unit' => [
          'name' => 'infbrit',
        ],
      ],
    ],
    'labels' => [
      0 => [
        'row' => 0,
        'col' => 2,
        'text' => [
          0 => clienttranslate('Tobrouk'),
        ],
      ],
      1 => [
        'row' => 1,
        'col' => 17,
        'text' => [
          0 => clienttranslate('Gambut'),
        ],
      ],
      2 => [
        'row' => 4,
        'col' => 6,
        'text' => [
          0 => clienttranslate('Belhamed'),
        ],
      ],
      3 => [
        'row' => 5,
        'col' => 1,
        'text' => [
          0 => clienttranslate('El Duda'),
        ],
      ],
      4 => [
        'row' => 6,
        'col' => 14,
        'text' => [
          0 => clienttranslate('Bir El Chleta'),
        ],
      ],
      5 => [
        'row' => 6,
        'col' => 24,
        'text' => [
          0 => clienttranslate('Bardia'),
        ],
      ],
      6 => [
        'row' => 7,
        'col' => 7,
        'text' => [
          0 => clienttranslate('Sidi Rezegh'),
        ],
      ],
      7 => [
        'row' => 8,
        'col' => 20,
        'text' => [
          0 => clienttranslate('Sidi Azeiz'),
        ],
      ],
      8 => [
        'row' => 11,
        'col' => 1,
        'text' => [
          0 => clienttranslate('Bir El Gobi'),
        ],
      ],
      9 => [
        'row' => 11,
        'col' => 21,
        'text' => [
          0 => clienttranslate('Capuzzo'),
        ],
      ],
      10 => [
        'row' => 14,
        'col' => 18,
        'text' => [
          0 => clienttranslate('Sidi Omar'),
        ],
      ],
      11 => [
        'row' => 14,
        'col' => 22,
        'text' => [
          0 => clienttranslate('Halfaya'),
        ],
      ],
      12 => [
        'row' => 14,
        'col' => 24,
        'text' => [
          0 => clienttranslate('Sollum'),
        ],
      ],
      13 => [
        'row' => 15,
        'col' => 9,
        'text' => [
          0 => clienttranslate('Gabr Saleh'),
        ],
      ],
    ],
  ],
  'text' => [
    'en' => [
      'name' => clienttranslate('Operation Crusader'),
      'description' => clienttranslate('Axis Player [Germany/Italy]
Take 6 Command cards.

Allied Player [Great Britain]
Take 6 Command cards.
You move first.'),
      'rules' => clienttranslate('North African Desert rules are in effect (Actions 9 - North African Desert Rules).

British Commonwealth Forces (Nation 5 - British Commonwealth Forces) and Italian Royal Army command rules (Nation 6 ? Italian Royal Army) are in effect.

Oasis Recovery rules are in effect at Bir el Chleta, Bir el Gobi and Gabr Saleh (Actions 10 - Oasis Recovery). Those rules also apply to each camp?s Campaign HQ.

Place a badge on the elite German infantry near Gambut (Troops 2 - Specialized Unit).

Place a badge on the German heavy artillery in Gambut and the British heavy artillery near Gabr Saleh. Both are Big Guns (Troops 3 ? Big Guns).

Place a badge on the mobile German artillery unit near Gambut (Troops 14 ? Mobile Artillery).

Place a badge on the two Afrikakorps units of Panzers with 4 tanks each (Troops 2 ? Specialized Unit).

The Axis artillery unit with a Battle Star is a Flak 88mm gun. Apply the following Heavy Anti-tank Guns rules : - Move 0-1 or battle at 2, 2, 2, 2. - Stars rolled score a hit on Armor. - Target must be in line of sight. - Ignore terrain battle protections.

The Allied player lays out the minefields around Tobruk and the Axis player those around Sidi Omar/Halfaya and Bir el Gobi (Terrain 29 -Minefields).

Air rules are optional. If used, give the Allied player one Air Sortie card and shuffle the other one in the deck, at game start.'),
      'historical' => clienttranslate('The Marmaric Desert, November 19, 1941 - General Auchinleck orders General Cunningham, to launch the British Eighth Army into a massive offensive into Libya in a bid to destroy the Italo-German armored divisions and free up the garrison in Tobruk. Over the next three weeks, over 700 British tanks face off against 240 German Panzers and 150 Italian tanks in desert battles around Sidi Rezegh and its airfield. The garrison in Tobruk even attempts a break out to connect with the Eight Army. After many engagements with varied outcomes, Rommel is forced to order the remnants of Panzergruppe Afrika to retreat toward Gazala, beaten not by his opponent\'s strategy, but rather its overwhelming material superiority. Tobruk is free!

The stage is set, the battle lines are drawn, and you are in command. The rest is history.'),
      'victory' => clienttranslate('12 Medals.

The German Medal in the Campaign Headquarters on the Allied baseline is a Permanent Medal Objective for the Axis player.'),
    ],
  ],
];
