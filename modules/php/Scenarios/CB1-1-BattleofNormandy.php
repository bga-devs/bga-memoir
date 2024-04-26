<?php
namespace M44\Scenarios;

$scenarios[40001] = [
    'campaignId' => '40001',
    'scenarios' => [
        'ALLIES' => [
            0 => 4187, // first scenario 4187-Securing the Flank
            1 => 4185, // if ALLIES won previous, play 4185-CapturingTheCrossing
            2 => 4186, // if ALLIES won previous, play 4186-WithdrawalFromHill112
            // 3 if ALLIES won previous, end campaign, move to Taking Caen
            
                
        ],
    ],
];