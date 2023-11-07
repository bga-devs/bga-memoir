<?php
namespace M44\Scenarios;


$scenarios[6687] =[
  'meta_data' => [
    'status' => 'PRIVATE',
    'software' => 'sed 1.2',
    'scenario_id' => '6687'
  ],
  'game_info' => [
    'date_begin' => '1944-06-06',
    'front' => 'WESTERN',
    'type' => 'HISTORICAL',
    'starting' => 'PLAYER2',
    'side_player1' => 'AXIS',
    'side_player2' => 'ALLIES',
    'country_player1' => 'DE',
    'country_player2' => 'US',
    'cards_player1' => 5,
    'cards_player2' => 5,
    'victory_player1' => 10,
    'victory_player2' => 10,
    'operationID' => '10',
    'options' => [
      'night_visibility_rules' => true,
      'blowbridge_opt2' => [
        'side' => 'ALLIES',
        'option' => 'NEED_NEIGHBOUR_UNIT',
      ],
      'airdrop' => [
         'side' => 'ALLIES',
         'range' => 4,
         'nbr_units' => [3,3,3], // nbr units for each drop
         'center' => 'G5',
         'behavior' => 'ONE_AT_TIME',
         'nbr_drops' => 3, // nbr of drops 'nbr_units' at a time
         'unit' => [
           'name' => 'inf2us',
         ],
       ],
    ],
  ],
  'board' => [
    'type' => 'BRKTHRU',
    'face' => 'COUNTRY',
    'hexagons' => [
      0 => [
        'row' => 0,
        'col' => 6,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
      ],
      1 => [
        'row' => 0,
        'col' => 14,
        'terrain' => [
          'name' => 'woods',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      2 => [
        'row' => 0,
        'col' => 22,
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'tankger',
        ],
      ],
      3 => [
        'row' => 1,
        'col' => 5,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
        'rect_terrain' => [
          'name' => 'bridge',
          'orientation' => 5,
          'behavior' => 'CAN_BE_BLOWN',
          'behavior2' => 'ONE_MEDAL_IF_BLOWN',
        ],
      ],
      4 => [
        'row' => 1,
        'col' => 7,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      5 => [
        'row' => 1,
        'col' => 19,
        'terrain' => [
          'name' => 'hedgerow',
        ],
      ],
      6 => [
        'row' => 2,
        'col' => 2,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      7 => [
        'row' => 2,
        'col' => 4,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
      ],
      8 => [
        'row' => 2,
        'col' => 12,
        'terrain' => [
          'name' => 'hedgerow',
        ],
        'unit' => [
          'name' => 'inf2us',
        ],
      ],
      9 => [
        'row' => 2,
        'col' => 22,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      10 => [
        'row' => 3,
        'col' => 3,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 1,
        ],
      ],
      11 => [
        'row' => 3,
        'col' => 7,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      12 => [
        'row' => 3,
        'col' => 9,
        'terrain' => [
          'name' => 'hedgerow',
        ],
      ],
      13 => [
        'row' => 3,
        'col' => 11,
        'tags' => [
          0 => [
            'name' => 'tag1',
          ],
        ],
      ],
      14 => [
        'row' => 3,
        'col' => 17,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      15 => [
        'row' => 4,
        'col' => 2,
        'terrain' => [
          'name' => 'buildings',
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
        'row' => 4,
        'col' => 4,
        'terrain' => [
          'name' => 'river',
          'orientation' => 3,
        ],
        'rect_terrain' => [
          'name' => 'bridge',
          'orientation' => 3,
          'behavior' => 'CAN_BE_BLOWN',
          'behavior2' => 'ONE_MEDAL_IF_BLOWN',
        ],
      ],
      17 => [
        'row' => 4,
        'col' => 12,
        'terrain' => [
          'name' => 'hedgerow',
        ],
      ],
      18 => [
        'row' => 4,
        'col' => 14,
        'terrain' => [
          'name' => 'hedgerow',
        ],
      ],
      19 => [
        'row' => 4,
        'col' => 18,
        'terrain' => [
          'name' => 'hedgerow',
        ],
      ],
      20 => [
        'row' => 4,
        'col' => 22,
        'terrain' => [
          'name' => 'hedgerow',
        ],
      ],
      21 => [
        'row' => 5,
        'col' => 5,
        'terrain' => [
          'name' => 'river',
          'orientation' => 3,
        ],
      ],
      22 => [
        'row' => 5,
        'col' => 7,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      23 => [
        'row' => 5,
        'col' => 17,
        'tags' => [
          0 => [
            'name' => 'tag1',
          ],
        ],
      ],
      24 => [
        'row' => 6,
        'col' => 4,
        'terrain' => [
          'name' => 'marshes',
        ],
      ],
      25 => [
        'row' => 6,
        'col' => 6,
        'terrain' => [
          'name' => 'river',
          'orientation' => 3,
        ],
      ],
      26 => [
        'row' => 6,
        'col' => 12,
        'terrain' => [
          'name' => 'hills',
        ],
      ],
      27 => [
        'row' => 6,
        'col' => 14,
        'terrain' => [
          'name' => 'marshes',
        ],
      ],
      28 => [
        'row' => 6,
        'col' => 16,
        'terrain' => [
          'name' => 'marshes',
        ],
      ],
      29 => [
        'row' => 7,
        'col' => 3,
        'terrain' => [
          'name' => 'marshes',
        ],
      ],
      30 => [
        'row' => 7,
        'col' => 5,
        'terrain' => [
          'name' => 'marshes',
        ],
      ],
      31 => [
        'row' => 7,
        'col' => 7,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 4,
        ],
      ],
      32 => [
        'row' => 7,
        'col' => 13,
        'terrain' => [
          'name' => 'marshes',
        ],
      ],
      33 => [
        'row' => 7,
        'col' => 15,
        'terrain' => [
          'name' => 'marshes',
        ],
      ],
      34 => [
        'row' => 7,
        'col' => 19,
        'terrain' => [
          'name' => 'buildings',
        ],
      ],
      35 => [
        'row' => 7,
        'col' => 23,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      36 => [
        'row' => 8,
        'col' => 4,
        'terrain' => [
          'name' => 'marshes',
        ],
      ],
      37 => [
        'row' => 8,
        'col' => 6,
        'terrain' => [
          'name' => 'riverFR',
          'orientation' => 2,
        ],
      ],
      38 => [
        'row' => 8,
        'col' => 8,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      39 => [
        'row' => 8,
        'col' => 10,
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
            'name' => 'medal1',
          ],
        ],
      ],
      40 => [
        'row' => 8,
        'col' => 12,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      41 => [
        'row' => 8,
        'col' => 14,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 5,
        ],
      ],
      42 => [
        'row' => 8,
        'col' => 16,
        'terrain' => [
          'name' => 'marshes',
        ],
      ],
      43 => [
        'row' => 9,
        'col' => 3,
        'terrain' => [
          'name' => 'marshes',
        ],
      ],
      44 => [
        'row' => 9,
        'col' => 5,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
      ],
      45 => [
        'row' => 9,
        'col' => 15,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 2,
        ],
      ],
      46 => [
        'row' => 9,
        'col' => 17,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      47 => [
        'row' => 9,
        'col' => 19,
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
            'name' => 'medal1',
          ],
        ],
      ],
      48 => [
        'row' => 9,
        'col' => 21,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      49 => [
        'row' => 9,
        'col' => 23,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      50 => [
        'row' => 10,
        'col' => 4,
        'terrain' => [
          'name' => 'river',
          'orientation' => 2,
        ],
      ],
      51 => [
        'row' => 10,
        'col' => 10,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      52 => [
        'row' => 10,
        'col' => 14,
        'terrain' => [
          'name' => 'hedgerow',
        ],
      ],
      53 => [
        'row' => 11,
        'col' => 1,
        'terrain' => [
          'name' => 'river',
          'orientation' => 1,
        ],
      ],
      54 => [
        'row' => 11,
        'col' => 3,
        'terrain' => [
          'name' => 'curve',
          'orientation' => 3,
        ],
      ],
      55 => [
        'row' => 11,
        'col' => 7,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      56 => [
        'row' => 11,
        'col' => 19,
        'tags' => [
          0 => [
            'name' => 'tag1',
          ],
        ],
      ],
      57 => [
        'row' => 11,
        'col' => 21,
        'terrain' => [
          'name' => 'hedgerow',
        ],
      ],
      58 => [
        'row' => 12,
        'col' => 12,
        'terrain' => [
          'name' => 'hedgerow',
        ],
      ],
      59 => [
        'row' => 12,
        'col' => 16,
        'terrain' => [
          'name' => 'hedgerow',
        ],
      ],
      60 => [
        'row' => 13,
        'col' => 3,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      61 => [
        'row' => 13,
        'col' => 7,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'inf2ger',
          'badge' => 'badge4',
        ],
      ],
      62 => [
        'row' => 13,
        'col' => 19,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      63 => [
        'row' => 14,
        'col' => 10,
        'terrain' => [
          'name' => 'hedgerow',
        ],
      ],
      64 => [
        'row' => 14,
        'col' => 14,
        'terrain' => [
          'name' => 'buildings',
        ],
      ],
      65 => [
        'row' => 14,
        'col' => 24,
        'terrain' => [
          'name' => 'buildings',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'infger',
        ],
      ],
      66 => [
        'row' => 15,
        'col' => 17,
        'terrain' => [
          'name' => 'church',
        ],
        'unit' => [
          'name' => 'infger',
        ],
        'tags' => [
          0 => [
            'name' => 'medal1',
          ],
        ],
      ],
      67 => [
        'row' => 15,
        'col' => 21,
        'terrain' => [
          'name' => 'woods',
        ],
      ],
      68 => [
        'row' => 16,
        'col' => 2,
        'terrain' => [
          'name' => 'woods',
        ],
        'obstacle' => [
          'name' => 'sand',
          'orientation' => 1,
        ],
        'unit' => [
          'name' => 'inf2ger',
          'badge' => 'badge4',
        ],
      ],
    ],
    'labels' => [
      0 => [
        'row' => 0,
        'col' => 8,
        'text' => [
          0 => 'Pont l\'Abbé',
        ],
      ],
      1 => [
        'row' => 1,
        'col' => 23,
        'text' => [
          0 => 'Amfreville',
        ],
      ],
      2 => [
        'row' => 2,
        'col' => 8,
        'text' => [
          0 => 'Picauville',
        ],
      ],
      3 => [
        'row' => 3,
        'col' => 1,
        'text' => [
          0 => 'Beuzeville',
        ],
      ],
      4 => [
        'row' => 6,
        'col' => 6,
        'text' => [
          0 => 'La Douve',
        ],
      ],
      5 => [
        'row' => 6,
        'col' => 12,
        'text' => [
          0 => 'Côte 30',
        ],
      ],
      6 => [
        'row' => 6,
        'col' => 20,
        'text' => [
          0 => 'Cauquigny',
        ],
      ],
      7 => [
        'row' => 8,
        'col' => 20,
        'text' => [
          0 => 'La Fière',
        ],
      ],
      8 => [
        'row' => 9,
        'col' => 9,
        'text' => [
          0 => 'Chef du Pont',
        ],
      ],
      9 => [
        'row' => 9,
        'col' => 15,
        'text' => [
          0 => 'Le Merderet',
        ],
      ],
      10 => [
        'row' => 12,
        'col' => 6,
        'text' => [
          0 => 'Les Forges',
        ],
      ],
      11 => [
        'row' => 13,
        'col' => 13,
        'text' => [
          0 => 'Fauville',
        ],
      ],
      12 => [
        'row' => 13,
        'col' => 23,
        'text' => [
          0 => 'Neuville',
          1 => 'au Plain',
        ],
      ],
      13 => [
        'row' => 14,
        'col' => 18,
        'text' => [
          0 => 'Ste Mère',
          1 => 'Eglise',
        ],
      ],
    ],
  ],
  'packs' => [
    'terrain' => 1,
  ],
  'text' => [
    'en' => [
      'name' => clienttranslate('Drop In The Night: 82nd'),
      'subtitle' => clienttranslate('Operation Boston'),
      'description' => clienttranslate('Axis Player [Germany]
Take 5 Command cards.

Allied player [United States]
Take 5 Command cards.
You move first.
Before your 1st turn, airdrop 9 units then play as normal.'),
      'rules' => clienttranslate('All Allied Infantry units are elite units (Troops 2 - Specialized Units). Badges are not required. Place a badge on the German elite infantry units (Troops 2 - Specialized units).

The Allied player may attempt to blow up the two bridges over the Douve River using option 2 (Actions 2 - Blowing Up Bridges). A bridge may be blown up only if an Allied infantry unit is on an adjacent hex.

Night Attacks rules are in effect (Actions 19 - Night Attacks). If you do not own the Air Pack or Pacific Theater expansion, use a 6-sided die. Place the 1 face up, to indicate an initial visibility of 1 hex. At the start of each turn, the Allied player rolls 4 Battle dice to see if the visibility changes. For each star rolled, increase the visibility range by 1, turning the 6-sided die on its appropriate side to reflect the increased visibility.

Use Paradrop rules (Actions 20 - Paradrop) for the Allied player. Before his first turn, the Allied player drops 3 elite Infantry units over each hex marked with a Battle Star (9 units total). You must aim at these hexes, but you do not lose your units if they land outside of the hex.

Once in full daylight, the Allied player can drop up to 3 additional Infantry units (using previously lost figures) over the hex at Les Forges by playing an appropriate Command card (so a probe in the Center allows 2 units to attempt an airdrop over Les Forges).'),
      'historical' => clienttranslate('During the night June 5-6 1944, paratroopers of the three parachute regiments of the 82nd "All American" US Airborne Division were dropped over the Cotentin peninsula. Their objective: to secure their drop zone, capture Sainte-Mère-Eglise and the bridges on the Merderet river and destroy the bridges on the Douve. Dropped in pitch black, the paras found themselves scattered all over the Normand bocage and countless swamps dotting the region, often far from their intended drop zones. Thankfully, the Germans camp was in disarray too, its troops disoriented by an enemy that seemed to pop out everywhere at once, and was thus only able to muster isolated counter-attacks.

By the morning of June 6, US reinforcements arrived, with Force "B" gliders landing near Les Forges. Operation Boston was a success.

The stage is set, the battle lines are drawn, and you are in command. The rest is history.'),
      'victory' => clienttranslate('10 medals.

Sainte-Mère-Eglise and the bridges of la Fière and Chef-du-Pont are Temporary Medal Objectives for the Allied forces.

The destruction of the bridges next to Pont-l\'Abbé and Beuzeville are Permanent Medal Objectives for the Allied forces.'),
    ],
  ],
];