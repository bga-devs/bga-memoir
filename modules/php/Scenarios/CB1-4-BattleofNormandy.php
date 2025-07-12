<?php
namespace M44\Scenarios;

// Small Campaign
$scenarios[40004] = [
    'campaignId' => '40004',
    'game_info' => [
        'date_begin' => '1944-07-31',
        'date_end' => '1944-08-22',
    ],
    'scenarios' => [
        'list' => [
            0 => 4272, // Saint Martin and Bull Bridge 
            1 => 4258, // Mont Pinçon
            2 => 4090, // Closing the Gap
        ],
        'name' => [
            0 => clienttranslate('Saint Martin and Bull Bridge'),
            1 => clienttranslate('Mont Pinçon'),
            2 => clienttranslate('Closing the Gap'),           
        ],
        'ALLIES' => [
            'reserve_tokens' => [
                0 => 3,
                1 => 0,
                2 => 0,
            ],
            'objectives_points' => [
                0 => -1,
                1 => 0,
                2 => 1,
                3 => 1,
                4 => 2,
            ],
            'country' => 'GB',
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
            'win_message' => [
                0 => clienttranslate('Hurry lads, while they\'re off balance! A strong push now will gain us the critical heights at Mont Pinçon. Make for that hill!'),
                1 => clienttranslate('Brilliant! Your ability to take theinitiative has brought us an unexpected opportunity. Make for Falaise and we can finally capture the remnants of the 7th Army!'),
                2 => clienttranslate('Ha! You Made that look easy old man! It\'s as I thought. We have brought an early end to the fighting here. Just a matter of mopping up now!'),
            ],
            'general' => 'Montgomery',
            'general_briefing' => clienttranslate('Operation Bluecoat is our chance to advance to Falaise and finally destroy the 7th German Army. If you press hard, I have no doubt that we will bring an early end to the fighting here in Normandy.\n\n        - General Montgomery'),                      
        ],

        'AXIS' => [
            'reserve_tokens' => [
                0 => 1,
                1 => 0,
                2 => 0,
            ],
            'objectives_points' => [
                0 => 0,
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
            'win_message' => [
                0 => clienttranslate('With such limited resources, you achieved a truly remarkable victory! Although the Allies can still advance, OKW believes they will be much more limit. Now, your next task is to prevent them from gaining the heights of Mont Pinçon.'),
                1 => clienttranslate('You proved there is still reason to fear the Whermacht! They cannot get to us from this avenue. They will have to try another way now.'),
                2 => clienttranslate('Though the Allies managed an unexpected breakthrough and captured many of our men. I know you attempted to make the most of it. I will talk to the Führer on your behalf...'),
            ],
            'general' => 'Von_Rundstedt',
            'general_briefing' => clienttranslate('Herr General, wake up! The Allies are pushing on towards Falaise sooner than we expected! We didn\'t have much warning, so there aren\'t many reserves, but the Führer wants us to hold at all costs. Do what you can...\n\n        - Generalfeldmarschall Von Rundstedt'),        
        ],
    ],
    'text' => [
        'en' => [
            'name' => clienttranslate('Early Falaise'),
            'subtitle' => clienttranslate('Operation Bluecoat, Falaise Gap'),
        ],
    ],
];