<?php
namespace M44\Scenarios;

// Small Campaign
$scenarios[40003] = [
    'campaignId' => '40003',
    'game_info' => [
        'date_begin' => '1944-07-08',
        'date_end' => '1944-07-18',
    ],
    'scenarios' => [
        'list' => [
            0 => 4087, // Drive On Caen 
            1 => 4089, // Night Withdrawal
            2 => 4088, // Pushing Through Caen
        ],
        'name' => [
            0 => clienttranslate('Drive on Caen'),
            1 => clienttranslate('Night Withdrawal'),
            2 => clienttranslate('Pushing through Caen'),           
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
                3 => 2,
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
            'win_message' => [
                0 => clienttranslate('Excellent! We can finally get on with taking Caen. Keep pushing and dont\'let Jerry refain his feet.'),
                1 => clienttranslate('Tell your lads they did a remarkable thing today! Kluge thought he could get away clean but you showed him we\'re made of sterner stuff, and netted us quite a few of the enemy in the process. They won\'t have any teeth left to bite us. Now is the time to push through Caen.'),
                2 => clienttranslate('Well done ! Caen is now behind us and we can get out into tank country. We can finally start using our numerical superiority to our advantage.'),
            ],
            'general' => 'Montgomery',
            'general_briefing' => clienttranslate('Right, now comes the time to take Caen. If we\'re ever to fully break out of this bridgehead there is no way around it, you must strike at Caen head on. Fight through the objective!\n\n        - General Montgomery'),               
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
            'win_message' => [
                    0 => clienttranslate('The OKW and the Führer were very impressed with your delaying tactics. Unfortunatelyour hold on Caen is no longer tenable. Do not lose heart: we aren\'t giving up the whole city. Make plans fro a niht withdrwal.'),
                    1 => clienttranslate('Tert book, Herr General! We can make a better stand here. They will no doubt keep coming but we can keep holding out. The longer they are delayed... well... just get ready nest Allies push.'),
                    2 => clienttranslate('You made the Allies fight hard for the ground they gained. Good! They will think twice before trying to advance so swiftly again.'),
                ],
            'general' => 'Von_Kluge',
            'general_briefing' => clienttranslate('I\'m sure you you know how vital Caen is. This crossroad is the key to the Allies breaking out of the bridgehead and into the open grounds of France. We cannot allow their armor to break into the open or their sheer numbers will overhelm us. Delay the Allies as long as possible. And remember, our reserves are sparse. \n\n        - Generalfeldmarschall Von Kluge'),
        ],
    ],
    'text' => [
        'en' => [
            'name' => clienttranslate('Taking Caen'),
            'subtitle' => clienttranslate('Operation Charnwood, Operation Atlantic'),
        ],
    ],
];