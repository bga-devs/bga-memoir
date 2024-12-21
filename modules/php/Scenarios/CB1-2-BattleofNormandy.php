<?php
namespace M44\Scenarios;

// Small Campaign
$scenarios[40002] = [
    'campaignId' => '40002',
    'scenarios' => [
        'list' => [
            0 => 4152, // Panzer Lehr Counter Attack 
            1 => 4172, // MartinVille Ridge
            2 => 15, // Operation Cobra
            3 => 16, // Couter Attack on Mortain
        ],
        'ALLIES' => [
            'reserve_tokens' => [
                0 => 3,
                1 => 0,
                2 => 0,
                3 => 1,
            ], 
            'country' => 'US',
            0 => 1, // if ALLIES won scenario play 1 => 4172, 
            1 => 2, // if ALLIES won scenario 1, play 2=> 15
            2 => 'END', // if ALLIES won scenario 2, END for small campaign
            3 => 'END', // if ALLIES won scenario 3, END for small campaign
            'reserve_roll_special' => [
                4152 => [
                    'flag_star' => 'advance2',
                    'star_star' => 'airpowertoken',
                ],
                4172 => [
                    'flag_star' => 'advance2',
                    'star_star' => 'airpowertoken',
                ],
                15 => [
                    'flag_star' => 'advance2',
                    'star_star' => 'airpowertoken',
                ],
                16 => [
                    'flag_star' => 'advance2',
                    'star_star' => 'airpowertoken',
                ],

            ],
            
                
        ],
        'AXIS' => [
            'reserve_tokens' => [
                0 => 2,
                1 => 0,
                2 => 0,
                3 => 1,
            ], 
            'country' => 'DE',
            0 => 1, // if AXIS won scenario 0, play  1=> 4172 MartinVille
            1 => 2, // if AXIS won scenario 1, play 2=> 15 Cobra
            2 => 3, // if AXIS won scenario 2, play 3=> 16 Mortain
            3 => 'END', // if AXIS won scenario 3, END for small campaign
            'reserve_roll_special' => [
                4152 => [
                    'flag_star' => 'wire',
                    'star_star' => 'airpowertoken',
                ],
                4172 => [
                    'flag_star' => 'wire',
                    'star_star' => 'airpowertoken',
                ],
                15 => [
                    'flag_star' => 'wire',
                    'star_star' => 'airpowertoken',
                ],
                16 => [
                    'flag_star' => 'wire',
                    'star_star' => 'airpowertoken',
                ],
            ],        
        ],
    ],
];