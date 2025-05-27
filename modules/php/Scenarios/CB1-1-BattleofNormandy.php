<?php
namespace M44\Scenarios;

// Small campaign Flanking Caen
$scenarios[40001] = [
    'campaignId' => '40001',
    'game_info' => [
        'date_begin' => '1944-06-25',
        'date_end' => '1944-07-10',
    ],
    'scenarios' => [
        'list' => [
            0 => 4187,
            1 => 4185,
            2 => 4186,
            3 => 1558,
        ],
        'name' => [
            0 => clienttranslate('Securing the flank'),
            1 => clienttranslate('Capturing the Crossing'),
            2 => clienttranslate('Withdrawal from Hill 112'),
            3 => clienttranslate('Hill 112'),            
        ],
        'ALLIES' => [
            'reserve_tokens' => [
                0 => 3,
                1 => 0,
                2 => 0,
                3 => 0,
            ],
            'objectives_points' => [
                0 => 0,
                1 => 2,
                2 => 2,
                3 => 3,
                4 => 3,
                5 => 4,
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
            'win_message' => [
                0 => clienttranslate('Good show ! With the flank secure, you can safely get across the river. Now we need to capture the bridge spanning the Odon to maintain our Momentum.\nDrive on in all Haste.'),
                1 => clienttranslate('Good progress ! This is going just as I planned. Now get the 11th Armoured Division to secure Hill 112 but be careful. We know Jerry has something up his sleeve.'),
                2 => clienttranslate('Very crafty of you ! I was concerned about Jerry\'s armour build up there but you\'ve put me at ease.'),
                3 => clienttranslate('Well done on a rather unexpected turn of events. With Hill 112 finally secured we can get on. I have to speak with Lt. Gen. Dempsey now, call the Second Army headquarters.'),
            ],
            'general' => 'Montgomery',
            'general_briefing' => clienttranslate('I\'ve been frustrated, old man, that we have not yet been able to take Caen. We have a new plan : Operation Epsom. I need you to lead the 15th Division across the Odon and secure a crossing so that we can push on to flank Caen. You\'ll have plenty of reserves. Start by securing the flank. Get the job done !\n\n        - General Montgomery'),                
        ],
        'AXIS' => [
            'reserve_tokens' => [
                0 => 1,
                1 => 0,
                2 => 0,
                3 => 0,
            ],
            'objectives_points' => [
                0 => -1,
                1 => 1,
            ],
            'country' => 'DE',
            0 => 1, // if AXIS won scenario 0, play  1=> 4185-CapturingTheCrossing 
            1 => 2, // if AXIS won scenario 1, play 2=> 4185-CapturingTheCrossing
            2 => 3, // if AXIS won scenario 2, play 3=> 4186-WithdrawalFromHill112
            3 => 'END', // if AXIS won scenario 3, END for small campaign
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
            'win_message' => [
                0 => clienttranslate('Well done Herr General. Without a secure flank to its side, we should be able to easily halt Tommy\'s main advance. He\'ll have to try and capture the crossing over the Odon next.'),
                1 => clienttranslate('Excellent, you have prevented the Allies from getting a solid bridgehead across the Odon. Their hold on Hill 112 is tentative as best. Use your forces to drive them back across the river and gain us some breathing room !'),
                2 => clienttranslate('Very good. The British are now in retreat and their operation has been halted thanks to your efforts. Be prepared because they will surely want to take the commanding heights of Hill 112 before they proceed any further. I have allocated you waht reserves I can.\nGood luck Herr General.'),
                3 => clienttranslate('Our Führer was pleased to hear you held the high ground. You made our fallen brothers proud today ! This British operation must be nearly spent and we are not much and we are not much worse for the wear.'),
            ],
            'general' => 'Rommel',
            'general_briefing' => clienttranslate('All our reports show that the British are preparing an operation. They must know we have been massing our armor to push them back into the sea, so they will no doubt start by trying to secure their flank. I can\'t offer you many reserves right now; regardless you must stop them. Remember sweat saves blood, blood saves lines, and brains save both; be smart and protect that flank!\n\n        - Generalfeldmarschall Rommel'),
        ],
    ],
    'text' => [
        'en' => [
            'name' => clienttranslate('Flanking Caen'),
            'subtitle' => clienttranslate('Operation Epsom, Operation Jupiter'),
        ],
    ],
];