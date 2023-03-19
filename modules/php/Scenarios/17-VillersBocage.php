<?php
namespace M44\Scenarios;

$scenarios[17] = [
    'meta_data'=> [
		'status'=> 'PRIVATE',
		'software'=> 'sed 1.2'
	],
	'game_info'=> [
		'date_begin'=> '1944-06-12',
		'front'=> 'WESTERN',
		'type'=> 'HISTORICAL',
		'starting'=> 'PLAYER1',
		'side_player1'=> 'AXIS',
		'side_player2'=> 'ALLIES',
		'country_player1'=> 'DE',
		'country_player2'=> 'GB',
		'cards_player1'=> 6,
		'cards_player2'=> 3,
		'victory_player1'=> 5,
		'victory_player2'=> 3,
		'operationID'=> '33'
	],
	'board'=> [
		'type'=> 'STANDARD',
		'face'=> 'COUNTRY',
		'hexagons'=> [
			[
				'row'=> 0,
				'col'=> 10,
				'terrain'=> [
					'name'=> 'woods',
					'behavior' => 'IMPASSABLE_HILL'
				],
				'unit'=> [
					'name'=> 'tank2ger',
					'nbr_units'=> '1'
				]
			],
			[
				'row'=> 0,
				'col'=> 18,
				'terrain'=> [
					'name'=> 'buildings',
					'behavior' => 'IMPASSABLE_HILL'
				]
			],
			[
				'row'=> 0,
				'col'=> 20,
				'terrain'=> [
					'name'=> 'road',
					'orientation'=> 2
				],
				'unit'=> [
					'name'=> 'tankbrit'
				],
				'tags'=> [
					[
						'name'=> 'medal2'
					]
				]
			],
			[
				'row'=> 0,
				'col'=> 24,
				'unit'=> [
					'name'=> 'tankbrit'
				]
			],
			[
				'row'=> 1,
				'col'=> 3,
				'terrain'=> [
					'name'=> 'woods',
					'behavior' => 'IMPASSABLE_HILL'
				]
			],
			[
				'row'=> 1,
				'col'=> 9,
				'terrain'=> [
					'name'=> 'woods',
					'behavior' => 'IMPASSABLE_HILL'
				],
				'unit'=> [
					'name'=> 'tank2ger',
					'nbr_units'=> '1',
					'equipment' => 'wittmann',
					'behavior' => 'IS_WITTMANN'
				],
				'tags' => [
					[
					  'name' => 'tag1',
					],
				],
			],
			[
				'row'=> 1,
				'col'=> 17,
				'terrain'=> [
					'name'=> 'buildings',
					'behavior' => 'IMPASSABLE_HILL'
				]
			],
			[
				'row'=> 1,
				'col'=> 19,
				'terrain'=> [
					'name'=> 'road',
					'orientation'=> 2
				]
			],
			[
				'row'=> 1,
				'col'=> 21,
				'terrain'=> [
					'name'=> 'buildings',
					'behavior' => 'IMPASSABLE_HILL'
				]
			],
			[
				'row'=> 2,
				'col'=> 6,
				'unit'=> [
					'name'=> 'tank2ger',
					'nbr_units'=> '1'
				]
			],
			[
				'row'=> 2,
				'col'=> 14,
				'terrain'=> [
					'name'=> 'woods',
					'behavior' => 'IMPASSABLE_HILL'
				]
			],
			[
				'row'=> 2,
				'col'=> 16,
				'terrain'=> [
					'name'=> 'buildings',
					'behavior' => 'IMPASSABLE_HILL'
				]
			],
			[
				'row'=> 2,
				'col'=> 18,
				'terrain'=> [
					'name'=> 'road',
					'orientation'=> 2
				]
			],
			[
				'row'=> 2,
				'col'=> 20,
				'unit'=> [
					'name'=> 'infbrit'
				]
			],
			[
				'row'=> 2,
				'col'=> 22,
				'terrain'=> [
					'name'=> 'buildings',
					'behavior' => 'IMPASSABLE_HILL'
				]
			],
			[
				'row'=> 3,
				'col'=> 1,
				'terrain'=> [
					'name'=> 'woods',
					'behavior' => 'IMPASSABLE_HILL'
				],
				'unit'=> [
					'name'=> 'tank2ger',
					'nbr_units'=> '1'
				]
			],
			[
				'row'=> 3,
				'col'=> 3,
				'terrain'=> [
					'name'=> 'woods',
					'behavior' => 'IMPASSABLE_HILL'
				],
				'unit'=> [
					'name'=> 'tank2ger',
					'nbr_units'=> '1'
				]
			],
			[
				'row'=> 3,
				'col'=> 9,
				'terrain'=> [
					'name'=> 'woods',
					'behavior' => 'IMPASSABLE_HILL'
				]
			],
			[
				'row'=> 3,
				'col'=> 13,
				'terrain'=> [
					'name'=> 'roadcurve',
					'orientation'=> 6
				]
			],
			[
				'row'=> 3,
				'col'=> 15,
				'terrain'=> [
					'name'=> 'road',
					'orientation'=> 1
				]
			],
			[
				'row'=> 3,
				'col'=> 17,
				'terrain'=> [
					'name'=> 'roadFL',
					'orientation'=> 1
				],
				'unit'=> [
					'name'=> 'infbrit'
				]
			],
			[
				'row'=> 3,
				'col'=> 19,
				'terrain'=> [
					'name'=> 'road',
					'orientation'=> 1
				],
				'unit'=> [
					'name'=> 'tankbrit'
				]
			],
			[
				'row'=> 3,
				'col'=> 21,
				'terrain'=> [
					'name'=> 'road',
					'orientation'=> 1
				]
			],
			[
				'row'=> 3,
				'col'=> 23,
				'terrain'=> [
					'name'=> 'road',
					'orientation'=> 1
				],
				'unit'=> [
					'name'=> 'tankbrit'
				],
				'tags'=> [
					[
						'name'=> 'medal2'
					]
				]
			],
			[
				'row'=> 4,
				'col'=> 6,
				'terrain'=> [
					'name'=> 'woods',
					'behavior' => 'IMPASSABLE_HILL'
				]
			],
			[
				'row'=> 4,
				'col'=> 12,
				'terrain'=> [
					'name'=> 'road',
					'orientation'=> 2
				],
				'unit'=> [
					'name'=> 'tankbrit'
				]
			],
			[
				'row'=> 4,
				'col'=> 16,
				'terrain'=> [
					'name'=> 'buildings',
					'behavior' => 'IMPASSABLE_HILL'
				]
			],
			[
				'row'=> 4,
				'col'=> 20,
				'terrain'=> [
					'name'=> 'buildings',
					'behavior' => 'IMPASSABLE_HILL'
				]
			],
			[
				'row'=> 4,
				'col'=> 22,
				'terrain'=> [
					'name'=> 'buildings',
					'behavior' => 'IMPASSABLE_HILL'
				]
			],
			[
				'row'=> 5,
				'col'=> 7,
				'terrain'=> [
					'name'=> 'roadcurve',
					'orientation'=> 6
				]
			],
			[
				'row'=> 5,
				'col'=> 9,
				'terrain'=> [
					'name'=> 'road',
					'orientation'=> 1
				],
				'unit'=> [
					'name'=> 'tankbrit'
				]
			],
			[
				'row'=> 5,
				'col'=> 11,
				'terrain'=> [
					'name'=> 'roadcurve',
					'orientation'=> 3
				]
			],
			[
				'row'=> 5,
				'col'=> 13,
				'terrain'=> [
					'name'=> 'woods',
					'behavior' => 'IMPASSABLE_HILL'
				]
			],
			[
				'row'=> 5,
				'col'=> 17,
				'terrain'=> [
					'name'=> 'buildings',
					'behavior' => 'IMPASSABLE_HILL'
				]
			],
			[
				'row'=> 6,
				'col'=> 0,
				'terrain'=> [
					'name'=> 'woods',
					'behavior' => 'IMPASSABLE_HILL'
				]
			],
			[
				'row'=> 6,
				'col'=> 2,
				'terrain'=> [
					'name'=> 'woods',
					'behavior' => 'IMPASSABLE_HILL'
				]
			],
			[
				'row'=> 6,
				'col'=> 4,
				'terrain'=> [
					'name'=> 'roadcurve',
					'orientation'=> 6
				]
			],
			[
				'row'=> 6,
				'col'=> 6,
				'terrain'=> [
					'name'=> 'roadcurve',
					'orientation'=> 3
				],
				'unit'=> [
					'name'=> 'tankbrit'
				]
			],
			[
				'row'=> 6,
				'col'=> 12,
				'terrain'=> [
					'name'=> 'woods',
					'behavior' => 'IMPASSABLE_HILL'
				]
			],
			[
				'row'=> 6,
				'col'=> 14,
				'unit'=> [
					'name'=> 'infbrit'
				]
			],
			[
				'row'=> 7,
				'col'=> 1,
				'terrain'=> [
					'name'=> 'road',
					'orientation'=> 1
				],
				'unit'=> [
					'name'=> 'infbrit'
				]
			],
			[
				'row'=> 7,
				'col'=> 3,
				'terrain'=> [
					'name'=> 'roadcurve',
					'orientation'=> 3
				],
				'unit'=> [
					'name'=> 'tankbrit'
				]
			],
			[
				'row'=> 7,
				'col'=> 5,
				'terrain'=> [
					'name'=> 'woods',
					'behavior' => 'IMPASSABLE_HILL'
				]
			],
			[
				'row'=> 7,
				'col'=> 7,
				'terrain'=> [
					'name'=> 'woods',
					'behavior' => 'IMPASSABLE_HILL'
				]
			],
			[
				'row'=> 7,
				'col'=> 9,
				'terrain'=> [
					'name'=> 'woods',
					'behavior' => 'IMPASSABLE_HILL'
				]
			],
			[
				'row'=> 7,
				'col'=> 17,
				'terrain'=> [
					'name'=> 'woods',
					'behavior' => 'IMPASSABLE_HILL'
				]
			],
			[
				'row'=> 7,
				'col'=> 21,
				'terrain'=> [
					'name'=> 'woods',
					'behavior' => 'IMPASSABLE_HILL'
				]
			],
		],
		'labels'=> [
			[
				'row'=> 1,
				'col'=> 9,
				'text'=> [
					'Wittmann'
				]
			],
			[
				'row'=> 2,
				'col'=> 18,
				'text'=> [
					'Villers-Bocage'
				]
			],
		],
	],
	'packs'=> [
		'terrain'=> 1,
		'mediterranean'=> 1
	],
	'text'=> [
		
		'en'=> [
			'name'=> 'Villers-Bocage',
			'subtitle'=> 'Operation Perche',
			'historical'=> 'On June 12th, the Second British Army was still searching for a soft spot in the German defenses around Caen. An attempt was made to exploit a gap between the 352nd German Division, driven back from Omaha by the American forces, and the Panzer Lehr, defending Caen.
			
			Good progress was made by the 7th Armored Division, it reached Villers Bocage, but the push came to a halt when the Division is leading elements were ambushed by Tiger tanks of the 501st SS heavy Tank battalion, just outside the small market town. Soon, twenty tanks were lost, including a reported ten credited to the German Tank ace Michael Wittmann alone!
			
			After the ambush, the 7 Armoured withdrew to a more secure position. The Second British Army had just lost its best chance of capturing Caen that month.
			
			The stage is set, the battle lines are drawn, and you are in command. The rest is history.',
			'description'=> 'Axis Player
			[Germany]
			Take 6 Command cards.
			You move first.
			
			Allied Player
			[British]
			Take 3 Command cards.',
			'victory'=> 'Axis Player=> 5 Medals.
			Allied Player=> 3 Medals.
			
			The Victory medals on the two road hexes exiting from the village are Permanent Medal Objectives for the Axis forces.',
			'rules'=> 'British Commonwealth Forces command rules are in effect (Nations 5 - British Commonwealth Forces).
			
			Tiger tank rules are in effect (Troops 16 - Tigers). The Tiger tank marked with a Battle Star is Michael Wittmann => It may ignore 1 flag, battle at +1d when not moving, and its Battle Star does not count as an additional Victory medal for the enemy.
			
			In this battle, all forest and village hexes are considered impassable. Those Axis units that do start in a forest hex may not enter any other forest or village hex once they leave their initial position.
			
			Air Rules are not in effect. The Air Sortie cards are set aside and are not used in this mission.'
		]
	],
];