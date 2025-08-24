<?php
namespace M44\Scenarios;

// Small Campaign
$scenarios[40005] = [
    'campaignId' => '40005',
    'game_info' => [
        'date_begin' => '1944-08-07',
        'date_end' => '1944-08-22',
    ],
    'scenarios' => [
        'list' => [
            0 => 4085, // Wittmann's final Battle 
            1 => 4086, // Opportunity at Falaise
            2 => 4090, // Closing the Gap
        ],
        'name' => [
            0 => clienttranslate('Wittmann\'s final Battle'),
            1 => clienttranslate('Opportunity at Falaise'),
            2 => clienttranslate('Closing the Gap'),           
        ],
        'ALLIES' => [
            'reserve_tokens' => [
                0 => 2,
                1 => 0,
                2 => 1,
            ],
            'objectives_points' => [
                0 => -1,
                1 => 0,
                2 => 1,
                3 => 1,
                4 => 2,
            ],
            'country' => 'US',
            0 => 1, // if ALLIES won scenario play 1 => 4086
            1 => 2, // if ALLIES won scenario 1, play 2=> 4090
            2 => 'END', // if ALLIES won scenario 2, END for small campaign
            'reserve_roll_special' => [
                4085 => [
                    'flag_star' => 'advance2',
                    'star_star' => 'airpowertoken',
                ],
                4086 => [
                    'flag_star' => 'advance2',
                    'star_star' => 'airpowertoken',
                ],
                4090 => [
                    'flag_star' => 'advance2',
                    'star_star' => 'airpowertoken',
                ],
            ],
            'win_message' => [
                0 => clienttranslate('I have reports that Wittmann, the Nazi tank ace, has just been knocked out. That will demoralize Jerry! On another note, we finally have the opportunity we wer looking for. Speed on to Falaise and we can catch the whole German 7th Army.'),
                1 => clienttranslate('While not an early ending, we still have a chance at catching Jerry. The yanks are coming up from the south, and with the 11th breaking through, we just need to close the gap to trap this pocket. I\'m counting on you to seal in the 7th Army tighter than a tent in a Sahara sand storm.'),
                2 => clienttranslate('We\'ve done it! Capturing the 7th must have rattled Jerry; the Germans are making desperate withdrawals all over France. We\'ll be celebrating in Berlin before Christmas!'),
            ],
            'general' => 'Montgomery',
            'general_briefing' => clienttranslate('This is it! We have a plan that will strike at the heart of Jerry\'s presence in Normandy. We\'ve had them on the run and now it\'s the time to catch them. Let\'s go to it, Lads.\n\n        - General Montgomery'),                      
        ],

        'AXIS' => [
            'reserve_tokens' => [
                0 => 1,
                1 => 0,
                2 => 1,
            ],
            'objectives_points' => [
                0 => 0,
            ],
            'country' => 'DE',
            0 => 1, // if AXIS won scenario 0, play  1=> 4086
            1 => 2, // if AXIS won scenario 1, play 2=> 4090
            2 => 'END', // if AXIS won scenario 2, END for small campaign
            'reserve_roll_special' => [
                4085 => [
                    'flag_star' => 'wire',
                    'star_star' => 'airpowertoken',
                ],
                // A CORRIGER AVT RELEASE
                4086 => [
                    'flag_star' => 'wire',
                    'star_star' => 'airpowertoken',
                ],
                4090 => [
                    'flag_star' => 'wire',
                    'star_star' => 'airpowertoken',
                ],
            ],
            'win_message' => [
                0 => clienttranslate('Alas our victory is sullied. I have reports that Hauptsturmführer wittmann has fallen. I fear this doesn\'t have well for us. Unfortunately we cannot honor him properly; the Allies are trying to trap us. Ensure they do not take Falaise.'),
                1 => clienttranslate('You just bought us precious time! The Allies are closing in now; there can be no doubt about that. But with the time you gave us, if we move move in haste, we may yet escape the noose. Get your forces out through the gap right now!'),
                2 => clienttranslate('Excellent! Thans to you, Herr General, our forces have achieved a miraculous escape. Many of our best men have broken free of the Falaise Pocket. The 7th Army will live to fight another day.'),
            ],
            'general' => 'Von_Kluge',
            'general_briefing' => clienttranslate('Although the Allies are relentless, they can bleed for ever. Continue to delay them, Herr General. We must make plans to get our forces out in good order. Reserves are at premium, so don\'t take any chances.\n\n        - Generalfeldmarschall Von Kluge'),        
        ],
    ],
    'text' => [
        'en' => [
            'name' => clienttranslate('Falaise'),
            'subtitle' => clienttranslate('Operation Totalize, Operation Tractable, Falaise Gap'),
        ],
    ],
];