<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * memoir implementation : ©  Timothée Pecatte <tim.pecatte@gmail.com>, Vincent Toper <vincent.toper@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * stats.inc.php
 *
 * memoir game statistics description
 *
 */

 require_once('modules/php/constants.inc.php');

$stats_type = [
  'table' => [
    'scenarioId' => [
      'id' => STAT_SCENARIO_ID,
      'name' => totranslate('ID of scenario played'),
      'type' => 'int',
    ],
  ],

  'value_labels' => [
    STAT_TEAM_FIRST_ROUND => [
      0 => totranslate('Allies'),
      1 => totranslate('Axis'),
    ],
    STAT_TEAM_SECOND_ROUND => [
      0 => totranslate('Allies'),
      1 => totranslate('Axis'),
    ],
    STAT_STATUS_FIRST_ROUND => [
      0 => totranslate('Loser'),
      1 => totranslate('Winner'),
    ],
    STAT_STATUS_SECOND_ROUND => [
      0 => totranslate('Loser'),
      1 => totranslate('Winner'),
    ],
  ],

  'player' => [
    'teamRound1' => [
      'id' => STAT_TEAM_FIRST_ROUND,
      'name' => totranslate('Round 1: played side'),
      'type' => 'int',
    ],
    'statusRound1' => [
      'id' => STAT_STATUS_FIRST_ROUND,
      'name' => totranslate('Round 1: status'),
      'type' => 'int',
    ],
    'medalRound1' => [
      'id' => STAT_MEDAL_FIRST_ROUND,
      'name' => totranslate('Round 1: earned medals'),
      'type' => 'int',
    ],
    'infUnitRound1' => [
      'id' => STAT_INF_UNIT_FIRST_ROUND,
      'name' => totranslate('Round 1: killed infantry units'),
      'type' => 'int',
    ],
    'armorUnitRound1' => [
      'id' => STAT_ARMOR_UNIT_FIRST_ROUND,
      'name' => totranslate('Round 1: killed armor units'),
      'type' => 'int',
    ],
    'artilleryUnitRound1' => [
      'id' => STAT_ARTILLERY_UNIT_FIRST_ROUND,
      'name' => totranslate('Round 1: killed artillery units'),
      'type' => 'int',
    ],
    'otherUnitRound1' => [
      'id' => STAT_OTHER_UNIT_FIRST_ROUND,
      'name' => totranslate('Round 1: killed other units'),
      'type' => 'int',
    ],
    'infFigRound1' => [
      'id' => STAT_INF_FIG_FIRST_ROUND,
      'name' => totranslate('Round 1: killed infantry figures'),
      'type' => 'int',
    ],
    'armorFigRound1' => [
      'id' => STAT_ARMOR_FIG_FIRST_ROUND,
      'name' => totranslate('Round 1: killed armor figures'),
      'type' => 'int',
    ],
    'artilleryFigRound1' => [
      'id' => STAT_ARTILLERY_FIG_FIRST_ROUND,
      'name' => totranslate('Round 1: killed artillery figures'),
      'type' => 'int',
    ],
    'otherFigRound1' => [
      'id' => STAT_OTHER_FIG_FIRST_ROUND,
      'name' => totranslate('Round 1: killed other figures'),
      'type' => 'int',
    ],

    'teamRound2' => [
      'id' => STAT_TEAM_SECOND_ROUND,
      'name' => totranslate('Round 2: played side'),
      'type' => 'int',
    ],
    'statusRound2' => [
      'id' => STAT_STATUS_SECOND_ROUND,
      'name' => totranslate('Round 2: status'),
      'type' => 'int',
    ],
    'medalRound2' => [
      'id' => STAT_MEDAL_SECOND_ROUND,
      'name' => totranslate('Round 2: earned medals'),
      'type' => 'int',
    ],
    'infUnitRound2' => [
      'id' => STAT_INF_UNIT_SECOND_ROUND,
      'name' => totranslate('Round 2: killed infantry units'),
      'type' => 'int',
    ],
    'armorUnitRound2' => [
      'id' => STAT_ARMOR_UNIT_SECOND_ROUND,
      'name' => totranslate('Round 2: killed armor units'),
      'type' => 'int',
    ],
    'artilleryUnitRound2' => [
      'id' => STAT_ARTILLERY_UNIT_SECOND_ROUND,
      'name' => totranslate('Round 2: killed artillery units'),
      'type' => 'int',
    ],
    'otherUnitRound1' => [
      'id' => STAT_OTHER_UNIT_SECOND_ROUND,
      'name' => totranslate('Round 2: killed other units'),
      'type' => 'int',
    ],
    'infFigRound2' => [
      'id' => STAT_INF_FIG_SECOND_ROUND,
      'name' => totranslate('Round 2: killed infantry figures'),
      'type' => 'int',
    ],
    'armorFigRound2' => [
      'id' => STAT_ARMOR_FIG_SECOND_ROUND,
      'name' => totranslate('Round 2: killed armor figures'),
      'type' => 'int',
    ],
    'artilleryFigRound2' => [
      'id' => STAT_ARTILLERY_FIG_SECOND_ROUND,
      'name' => totranslate('Round 2: killed artillery figures'),
      'type' => 'int',
    ],
    'otherFigRound2' => [
      'id' => STAT_OTHER_FIG_SECOND_ROUND,
      'name' => totranslate('Round 2: killed other figures'),
      'type' => 'int',
    ],

    'diceCount' => [
      'id' => STAT_DICE_COUNT,
      'name' => totranslate('Number of dice rolled'),
      'type' => 'int',
    ],

    'diceInf' => [
      'id' => STAT_DICE_INF,
      'name' => totranslate('Number of infantry dice obtained'),
      'type' => 'int',
    ],
    'diceArmor' => [
      'id' => STAT_DICE_ARMOR,
      'name' => totranslate('Number of armor dice obtained'),
      'type' => 'int',
    ],
    'diceGrenade' => [
      'id' => STAT_DICE_GRENADE,
      'name' => totranslate('Number of grenade dice obtained'),
      'type' => 'int',
    ],
    'diceStar' => [
      'id' => STAT_DICE_STAR,
      'name' => totranslate('Number of star dice obtained'),
      'type' => 'int',
    ],
    'diceFlag' => [
      'id' => STAT_DICE_FLAG,
      'name' => totranslate('Number of flag dice obtained'),
      'type' => 'int',
    ],
  ],
];
