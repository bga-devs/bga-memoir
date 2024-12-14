<?php
namespace M44\Scenarios;

$scenarios[40001] = [
    'campaignId' => '40001',
    'scenarios' => [
        'list' => [
            0 => 4187,
            1 => 4185,
            2 => 4186,
            3 => 1558,
        ],
        'ALLIES' => [
            'reserve_tokens' => [
                0 => 3,
                1 => 0,
                2 => 0,
                3 => 0,
            ], 
            'country' => 'GB',
            0 => 1, // if ALLIES won scenario 0 => 4187, 
            1 => 2, // if ALLIES won scenario 1, play 1=> 4185-CapturingTheCrossing
            2 => 'END', // if ALLIES won scenario 2, END for small campaign
            3 => 'END', // if ALLIES won scenario 3, END for small campaign
            'reserve_roll_special' => [
                4187 => [
                    'flag_star' => 'advance2',
                    'star_star' => 'airpowertoken',
                ],
                4185 => [
                    'flag_star' => 'advance2',
                    'star_star' => 'airpowertoken',
                ],
                4186 => [
                    'flag_star' => 'advance2',
                    'star_star' => 'airpowertoken',
                ],
                1558 => [
                    'flag_star' => 'advance2',
                    'star_star' => 'airpowertoken',
                ],

            ],
            
                
        ],
        'AXIS' => [
            'reserve_tokens' => [
                0 => 1,
                1 => 0,
                2 => 0,
                3 => 0,
            ], 
            'country' => 'DE',
            0 => 1, // if AXIS won scenario 0, play  1=> 4185-CapturingTheCrossing 
            1 => 2, // if AXIS won scenario 1, play 2=> 4185-CapturingTheCrossing
            2 => 3, // if AXIS won scenario 2, play 3=> 4186-WithdrawalFromHill112
            3 => 'END', // if AXIS won scenario 3, END for small campaign
            'reserve_roll_special' => [
                4187 => [
                    'flag_star' => 'wire',
                    'star_star' => 'airpowertoken',
                ],
                4185 => [
                    'flag_star' => 'wire',
                    'star_star' => 'airpowertoken',
                ],
                4186 => [
                    'flag_star' => 'wire',
                    'star_star' => 'airpowertoken',
                ],
                1558 => [
                    'flag_star' => 'wire',
                    'star_star' => 'airpowertoken',
                ],
            ],        
        ],
    ],
];