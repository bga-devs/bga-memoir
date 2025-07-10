<?php
namespace M44\Scenarios;

// Small Campaign
$scenarios[40002] = [
    'campaignId' => '40002',
    'game_info' => [
        'date_begin' => '1944-07-11',
        'date_end' => '1944-08-07',
    ],
    'scenarios' => [
        'list' => [
            0 => 4152, // Panzer Lehr Counter Attack 
            1 => 4172, // MartinVille Ridge
            2 => 15, // Operation Cobra
            3 => 16, // Counter Attack on Mortain
        ],
        'name' => [
            0 => clienttranslate('Panzer Lehr Counter Attack'),
            1 => clienttranslate('MartinVille Ridge'),
            2 => clienttranslate('Operation Cobra'),
            3 => clienttranslate('Counter Attack on Mortain'),            
        ],
        'ALLIES' => [
            'reserve_tokens' => [
                0 => 3,
                1 => 0,
                2 => 0,
                3 => 1,
            ],
            'objectives_points' => [
                0 => 0,
                1 => 1,
                2 => 2,
                3 => 2,
                4 => 3,
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
            'win_message' => [
                0 => clienttranslate('That should seriously affect their ability to do anything like that again. Now move on to Saint-Lô and capture the high ground that looks down on it. The 15th is already well in advance of where they should be, help them out.'),
                1 => clienttranslate('Your victory here at Saint-Lô has saved us hundreds of lives and will get our breakout operation under way with moraleat an all time high. Keep it up and you may be looking at a promotion.'),
                2 => clienttranslate('The breakout is picking up momentum. Now we can strike decisively to end the fighting in Normandy. Patton is eager to exploit your breakout.'),
                3 => clienttranslate('What was Hitler thinking? Well I\'m glad you put an end to that operation. While we lost some men here, it could have been much worse.'),
            ],
            'general' => 'Bradley',
            'general_briefing' => clienttranslate('I know it\'s been hell getting through these hedgerows, boys, but I\'ve got a plan to get us out. Operation Cobra wiil see us finally break out into the open. The thing is we have to crack Saint-Lô first and I have early reports thta Panzer Lehr is starting an operation thta could put a hitch in our giddy-up. Get out there and stop them, then move on quick as you can to Saint-Lô!\n\n        - General Bradley'),
                
        ],
        'AXIS' => [
            'reserve_tokens' => [
                0 => 2,
                1 => 0,
                2 => 0,
                3 => 1,
            ],
            'objectives_points' => [
                0 => -2,
                1 => -1,
                2 => 0,
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
            'win_message' => [
                0 => clienttranslate('That will give the Americans something to think about. They are certainly massing for something, but if we can contain them in hedgerow country... Hold Saint-Lô at all cost!'),
                1 => clienttranslate('The Americans are certainly up to something but without the advantage of Saint-Lô as a starting point, they will have a hard time. Be prepared for an American offensive in your sector, Herr General.'),
                2 => clienttranslate('Pathetic: Americans soldiers cannot whithstand the Wehrmacht. I have orders from the Führer to proceed with Unternehemen Lüttich. You will be in the command of the operation. You know we don\'t have many reserves... Do what you can. For the Fatherland!'),
                3 => clienttranslate('Ha! It looks like our little gamble paid off. Hitler will be pleased. The Allied froces are many but puny against our fearless Wehrmacht!'),
            ],
            'general' => 'Von_Rundstedt',
            'general_briefing' => clienttranslate('I\'m concerned about the American forces making progress on our flank, so I\'ve shifted Panzer Lehr to your command. Make good use of them. Hitler is insisting on a massive counter-attack and we must be in good standing when the order comes. For the Führer!\n\n        - Generalfeldmarschall Von Rundstedt'),       
        ],
    ],
    'text' => [
        'en' => [
            'name' => clienttranslate('The Breakout'),
            'subtitle' => clienttranslate('Battle for Saint-Lô, Operation Cobra, Unternehemen Lüttich'),
        ],
    ],
];