<?php
namespace M44\Scenarios;

// Small Campaign
$scenarios[40003] = [
    'campaignId' => '40003',
    'scenarios' => [
        'list' => [
            0 => 4087, // Drive On Caen 
            1 => 4089, // Night Withdrawal
            2 => 4088, // Pushing Through Caen
        ],
        'ALLIES' => [
            'reserve_tokens' => [
                0 => 3,
                1 => 0,
                2 => 0,
            ], 
            'country' => 'GB',
            0 => 1, // if ALLIES won scenario play 1 => 4089
            1 => 2, // if ALLIES won scenario 1, play 2=> 4088
            2 => 'END', // if ALLIES won scenario 2, END for small campaign
            
            'reserve_roll_special' => [
                4087 => [
                    'flag_star' => 'advance2',
                    'star_star' => 'airpowertoken',
                ],
                4089 => [
                    'flag_star' => 'advance2',
                    'star_star' => 'airpowertoken',
                ],
                4088 => [
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
            0 => 1, // if AXIS won scenario 0, play  1=> 4172 MartinVille
            1 => 2, // if AXIS won scenario 1, play 2=> 15 Cobra
            2 => 'END', // if AXIS won scenario 2, END for small campaign
            'reserve_roll_special' => [
                4087 => [
                    'flag_star' => 'wire',
                    'star_star' => 'airpowertoken',
                ],
                4089 => [
                    'flag_star' => 'wire',
                    'star_star' => 'airpowertoken',
                ],
                4088 => [
                    'flag_star' => 'wire',
                    'star_star' => 'airpowertoken',
                ],
            ],        
        ],
    ],
];