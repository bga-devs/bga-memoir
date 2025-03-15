<?php
namespace M44\Scenarios;

// Small Campaign
$scenarios[40004] = [
    'campaignId' => '40004',
    'scenarios' => [
        'list' => [
            0 => 4272, // Saint Martin and Bull Bridge 
            1 => 4258, // Mont Pinçon
            2 => 4090, // Closing the Gap
        ],
        'ALLIES' => [
            'reserve_tokens' => [
                0 => 3,
                1 => 0,
                2 => 0,
            ], 
            'country' => 'US',
            0 => 1, // if ALLIES won scenario play 1 => 4258
            1 => 2, // if ALLIES won scenario 1, play 2=> 4090
            2 => 'END', // if ALLIES won scenario 2, END for small campaign
            
            'reserve_roll_special' => [
                4272 => [
                    'flag_star' => 'advance2',
                    'star_star' => 'airpowertoken',
                ],
                4258 => [
                    'flag_star' => 'advance2',
                    'star_star' => 'airpowertoken',
                ],
                4090 => [
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
            ], 
            'country' => 'DE',
            0 => 1, // if AXIS won scenario 0, play  1=> 4258 Mont Pinçon
            1 => 'END', // if AXIS won scenario 1, END for small campaign
            2 => 'END', // if AXIS won scenario 2, END for small campaign
            'reserve_roll_special' => [
                4272 => [
                    'flag_star' => 'wire',
                    'star_star' => 'airpowertoken',
                ],
                4258 => [
                    'flag_star' => 'wire',
                    'star_star' => 'airpowertoken',
                ],
                4090 => [
                    'flag_star' => 'wire',
                    'star_star' => 'airpowertoken',
                ],
            ],        
        ],
    ],
];