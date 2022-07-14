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
 * states.inc.php
 *
 * memoir game states description
 *
 */

$machinestates = [
  // The initial state. Please do not modify.
  ST_GAME_SETUP => [
    'name' => 'gameSetup',
    'description' => '',
    'type' => 'manager',
    'action' => 'stGameSetup',
    'transitions' => ['' => ST_START_TABLE],
  ],

  ST_START_TABLE => [
    'name' => 'startTable',
    'description' => '',
    'descriptionmyturn' => '',
    'type' => 'game',
    'action' => 'stStartTable',
    'possibleactions' => [],
    'transitions' => ['upload' => ST_UPLOAD_SCENARIO],
  ],

  ST_UPLOAD_SCENARIO => [
    'name' => 'uploadScenario',
    'description' => clienttranslate('You must upload a m44 scenario'),
    'descriptionmyturn' => clienttranslate('${you} must upload a m44 scenario'),
    'type' => 'multipleactiveplayer',
    'possibleactions' => ['actUploadScenario'],
    'transitions' => [
      '' => ST_NEW_ROUND,
    ],
  ],

  ST_NEW_ROUND => [
    'name' => 'newRound',
    'description' => '',
    'descriptionmyturn' => '',
    'type' => 'game',
    'action' => 'stNewRound',
    'possibleactions' => [],
    'transitions' => ['' => ST_NEW_ROUND],
  ],

  ST_AIR_DROP => [
    'name' => 'airDrop',
    'description' => clienttranslate('${actplayer} must choose where to air drop ${nb} units'),
    'descriptionmyturn' => clienttranslate('${you} must choose where to air drop ${nb} units'),
    'type' => 'activeplayer',
    'args' => 'argsAirDrop',
    'possibleactions' => ['actAirDrop'],
    'transitions' => [
      '' => ST_PREPARE_TURN,
    ],
  ],

  ST_END_OF_ROUND => [
    'name' => 'endOfRound',
    'description' => '',
    'descriptionmyturn' => '',
    'type' => 'game',
    'action' => 'stEndOfRound',
    'possibleactions' => [],
    'transitions' => ['change' => ST_CHANGE_OF_ROUND, 'end' => ST_END_OF_GAME],
  ],

  ST_CHANGE_OF_ROUND => [
    'name' => 'changeOfRound',
    'description' => clienttranslate('Waiting for the other team to proceed to next round'),
    'descriptionmyturn' => clienttranslate('Round 1 is over: ${team} wins!'),
    'type' => 'multipleactiveplayer',
    'possibleactions' => ['actProceed'],
    'args' => 'argsChangeOfRound',
    'transitions' => ['done' => ST_NEW_ROUND],
  ],

  ///////////////////////////////////////////////////////
  //   ____            _        _____ _
  //  | __ )  __ _ ___(_) ___  |  ___| | _____      __
  //  |  _ \ / _` / __| |/ __| | |_  | |/ _ \ \ /\ / /
  //  | |_) | (_| \__ \ | (__  |  _| | | (_) \ V  V /
  //  |____/ \__,_|___/_|\___| |_|   |_|\___/ \_/\_/
  ///////////////////////////////////////////////////////
  ST_PREPARE_TURN => [
    'name' => 'prepareTurn',
    'description' => '',
    'descriptionmyturn' => '',
    'type' => 'game',
    'action' => 'stPrepareTurn',
    'updateGameProgression' => true,
    'transitions' => ['playCard' => ST_PLAY_CARD, 'commissar' => ST_COMMISSAR],
  ],

  ST_COMMISSAR => [
    'name' => 'commissarCard',
    'description' => clienttranslate('${actplayer} must put a card under commissar token'),
    'descriptionmyturn' => clienttranslate('${you} must put a card under commissar token'),
    'type' => 'activeplayer',
    'args' => 'argsCommissarCard',
    'possibleactions' => ['actCommissarCard', 'actPlayCard'],
    'transitions' => [
      'play' => ST_PLAY_COMMISSAR,
      'selectUnits' => ST_ORDER_UNITS,
      'counterAttack' => ST_COUNTER_ATTACK,
    ],
  ],

  ST_PLAY_COMMISSAR => [
    'name' => 'playCommissarCard',
    'description' => clienttranslate('${actplayer} must choose how/where to play the card'),
    'descriptionmyturn' => clienttranslate('${you} must choose how/where to play the card'),
    'type' => 'activeplayer',
    'action' => 'stPlayCommissarCard',
    'args' => 'argsPlayCommissarCard',
    'possibleactions' => ['actRestart', 'actPlayCommissarCard'],
    'transitions' => [
      'selectUnits' => ST_ORDER_UNITS,
      'finestHour' => ST_FINEST_HOUR_ROLL,
      'airpower' => ST_AIRPOWER_TARGET,
      'barrage' => ST_BARRAGE_TARGET,
      'medics' => ST_MEDICS_TARGET,
      'counterAttack' => ST_COUNTER_ATTACK,
      'medicsBT' => ST_MEDICS_BT_ROLL,
    ],
  ],

  ST_PLAY_CARD => [
    'name' => 'playCard',
    'description' => clienttranslate('${actplayer} must play a card'),
    'descriptionmyturn' => clienttranslate('${you} must play a card'),
    'type' => 'activeplayer',
    'args' => 'argsPlayCard',
    'action' => 'stPlayCard',
    'possibleactions' => ['actPlayCard'],
    'transitions' => [
      'selectUnits' => ST_ORDER_UNITS,
      'finestHour' => ST_FINEST_HOUR_ROLL,
      'airpower' => ST_AIRPOWER_TARGET,
      'barrage' => ST_BARRAGE_TARGET,
      'medics' => ST_MEDICS_TARGET,
      'counterAttack' => ST_COUNTER_ATTACK,
      'commissar' => ST_COMMISSAR,
      'medicsBT' => ST_MEDICS_BT_ROLL,
    ],
  ],

  ST_ORDER_UNITS => [
    'name' => 'orderUnits',
    'description' => clienttranslate('${actplayer} may order ${nTitle} unit(s) ${desc}'),
    'descriptionmyturn' => clienttranslate('${you} may order ${nTitle} unit(s) ${desc}'),
    'type' => 'activeplayer',
    'args' => 'argsOrderUnits',
    'action' => 'stOrderUnits',
    'possibleactions' => ['actOrderUnits', 'actRestart'],
    'transitions' => ['moveUnits' => ST_MOVE_UNITS, 'digIn' => ST_DIG_IN],
  ],

  ST_MOVE_UNITS => [
    'name' => 'moveUnits',
    'description' => clienttranslate('${actplayer} may move activated units'),
    'descriptionmyturn' => clienttranslate('${you} may move activated units'),
    'type' => 'activeplayer',
    'args' => 'argsMoveUnits',
    'action' => 'stMoveUnits',
    'possibleactions' => [
      'actRestart',
      'actMoveUnit',
      'actMoveUnitsDone',
      'actHealUnit',
      'actHealUnitHospital',
      'actExitUnit',
    ],
    'transitions' => ['moveUnits' => ST_MOVE_UNITS, 'attackUnits' => ST_PRE_ATTACK],
  ],

  ST_PRE_ATTACK => [
    'name' => 'preAttackUnits',
    'description' => '',
    'descriptionmyturn' => '',
    'type' => 'game',
    'action' => 'stPreAttackUnits',
    'transitions' => [
      '' => ST_ATTACK,
    ],
  ],

  ST_ATTACK => [
    'name' => 'attackUnits',
    'description' => clienttranslate('${actplayer} may battle'),
    'descriptionmyturn' => clienttranslate('${you} may select the unit to battle with'),
    'type' => 'activeplayer',
    'action' => 'stAttackUnits',
    'args' => 'argsAttackUnit',
    'possibleactions' => [
      'actRestart',
      'actAttackUnit',
      'actAttackUnitsDone',
      'actRemoveWire',
      'actRemoveRoadBlock',
      'actSealCave',
    ],
    'transitions' => [
      'ambush' => ST_OPPONENT_AMBUSH,
      'attack' => ST_ATTACK,
      'draw' => ST_CONFIRM_TURN,
      'moveAgain' => ST_PRE_MOVE_AGAIN,
    ],
  ],

  ST_ATTACK_THROW => [
    'name' => 'attackThrow',
    'description' => '',
    'descriptionmyturn' => '',
    'type' => 'game',
    'action' => 'stAttackThrow',
    'transitions' => [
      'retreat' => ST_ATTACK_RETREAT,
      'nextAttack' => ST_ATTACK_THROW,
      'takeGround' => ST_TAKING_GROUND,
      'battleBack' => ST_BATTLE_BACK,
    ],
  ],

  ST_BATTLE_BACK => [
    'name' => 'battleBack',
    'description' => clienttranslate('${actplayer} may battle back with 1 die'),
    'descriptionmyturn' => clienttranslate('${you} may battle back with 1 die'),
    'type' => 'activeplayer',
    'args' => 'argsBattleBack',
    // 'action' => 'stRetreatUnit',
    'possibleactions' => ['actBattleBack', 'actBattleBackPass'],
    'transitions' => [
      'retreat' => ST_AMBUSH_RESOLVE,
      'nextAttack' => ST_ATTACK,
    ],
  ],

  ST_ATTACK_RETREAT => [
    'name' => 'attackRetreat',
    'description' => clienttranslate('${actplayer} must retreat the unit ${min} hex(es) ${desc}'),
    'descriptionmyturn' => clienttranslate('${you} must retreat the unit ${min} hex(es) ${desc}'),
    'descriptionskippable' => clienttranslate('${actplayer} may retreat the unit up to ${max} hex(es) (optional)'),
    'descriptionmyturnskippable' => clienttranslate('${you} may retreat the unit up to ${max} hex(es) (optional)'),
    'type' => 'activeplayer',
    'args' => 'argsRetreatUnit',
    'action' => 'stRetreatUnit',
    'possibleactions' => ['actRetreatUnit', 'actRetreatUnitDone', 'actIgnore1Flag'],
    'transitions' => [
      'retreat' => ST_ATTACK_RETREAT,
      'nextAttack' => ST_ATTACK_THROW,
      'takeGround' => ST_TAKING_GROUND,
      'battleBack' => ST_BATTLE_BACK,
    ],
  ],

  ST_CONFIRM_TURN => [
    'name' => 'confirmTurn',
    'description' => clienttranslate('${actplayer} must confirm or restart their turn'),
    'descriptionmyturn' => clienttranslate('${you} must confirm or restart your turn'),
    'type' => 'activeplayer',
    'args' => 'argsConfirmTurn',
    'action' => 'stConfirmTurn',
    'possibleactions' => ['actConfirmTurn', 'actRestart'],
    'transitions' => ['confirm' => ST_DRAW],
  ],

  ST_DRAW => [
    'name' => 'drawCard',
    'description' => '',
    'type' => 'game',
    'action' => 'stDrawCard',
    'transitions' => ['endRound' => ST_END_TURN, 'choice' => ST_DRAW_CHOICE],
  ],

  ST_DRAW_CHOICE => [
    'name' => 'drawChoice',
    'description' => clienttranslate('${actplayer} must choose which card to discard'),
    'descriptionmyturn' => clienttranslate('${you} must choose which card to discard'),
    'type' => 'activeplayer',
    'args' => 'argsDrawChoice',
    'possibleactions' => ['actChooseCard'],
    'transitions' => ['endRound' => ST_END_TURN],
  ],

  ST_END_TURN => [
    'name' => 'endTurn',
    'description' => '',
    'descriptionmyturn' => '',
    'type' => 'game',
    'action' => 'stEndTurn',
    'transitions' => ['next' => ST_PREPARE_TURN],
  ],

  ////////////////////////////////////////////////////////////////
  //  _____     _           ____                           _
  // |_   _|_ _| | _____   / ___|_ __ ___  _   _ _ __   __| |
  //   | |/ _` | |/ / _ \ | |  _| '__/ _ \| | | | '_ \ / _` |
  //   | | (_| |   <  __/ | |_| | | | (_) | |_| | | | | (_| |
  //   |_|\__,_|_|\_\___|  \____|_|  \___/ \__,_|_| |_|\__,_|
  ///////////////////////////////////////////////////////////////
  ST_TAKING_GROUND => [
    'name' => 'takeGround',
    'description' => clienttranslate('${actplayer} may take the ground'),
    'descriptionmyturn' => clienttranslate('${you} may take the ground'),
    'type' => 'activeplayer',
    'args' => 'argsTakeGround',
    'action' => 'stTakeGround',
    'possibleactions' => ['actTakeGround', 'actPassTakeGround'],
    'transitions' => [
      'next' => ST_ATTACK,
      'nextAttack' => ST_ATTACK_THROW,
      'desertMove' => ST_DESERT_MOVE,
      'overrun' => ST_ARMOR_OVERRUN,
    ],
  ],

  ST_DESERT_MOVE => [
    'name' => 'desertMove',
    'description' => clienttranslate('${actplayer} may move an additional hex (Desert rules)'),
    'descriptionmyturn' => clienttranslate('${you}  may move an additional hex (Desert rules)'),
    'type' => 'activeplayer',
    'args' => 'argsDesertMove',
    'action' => 'stDesertMove',
    'possibleactions' => ['actMoveUnit', 'actMoveUnitsDone', 'actExitUnit'],
    'transitions' => [
      'overrun' => ST_ARMOR_OVERRUN,
      'nextAttack' => ST_ATTACK_THROW,
    ],
  ],

  ST_ARMOR_OVERRUN => [
    'name' => 'armorOverrun',
    'description' => clienttranslate('${actplayer} may attack an unit (Armor overrun)'),
    'descriptionmyturn' => clienttranslate('${you} may attack an unit (Armor overrun)'),
    'type' => 'activeplayer',
    'args' => 'argsArmorOverrun',
    'action' => 'stArmorOverrun',
    'possibleactions' => ['actAttackUnit', 'actNextAttack', 'actAttackUnitsDone'],
    'transitions' => [
      'ambush' => ST_OPPONENT_AMBUSH,
      'next' => ST_ATTACK, // if player doesn't attack, no new taking ground
      'nextAttack' => ST_ATTACK,
    ],
  ],

  ////////////////////////////////////////////////
  //     _              _               _
  //    / \   _ __ ___ | |__  _   _ ___| |__
  //   / _ \ | '_ ` _ \| '_ \| | | / __| '_ \
  //  / ___ \| | | | | | |_) | |_| \__ \ | | |
  // /_/   \_\_| |_| |_|_.__/ \__,_|___/_| |_|
  ////////////////////////////////////////////////
  ST_OPPONENT_AMBUSH => [
    'name' => 'opponentAmbush',
    'description' => clienttranslate('${actplayer} can react to the attack'),
    'descriptionmyturn' => clienttranslate('${you} can react to the attack'),
    'descriptionmyturnnooption' => clienttranslate('${you} can\'t react to the attack'),
    'type' => 'activeplayer',
    'args' => 'argsOpponentAmbush',
    'action' => 'stAmbush',
    'possibleactions' => ['actAmbush', 'actPassAmbush'],
    'transitions' => ['pass' => ST_ATTACK_THROW, 'retreat' => ST_AMBUSH_RESOLVE],
  ],

  ST_AMBUSH_RESOLVE => [
    'name' => 'ambushResolve',
    'description' => clienttranslate('${actplayer} must retreat the unit ${min} hex(es) ${desc} (Ambush effect)'),
    'descriptionmyturn' => clienttranslate('${you} must retreat the unit ${min} hex(es) ${desc} (Ambush effect)'),
    'descriptionskippable' => clienttranslate('${actplayer} may retreat the unit up to ${max} hex(es) (Ambush effect)'),
    'descriptionmyturnskippable' => clienttranslate('${you} may retreat the unit up to ${max} hex(es) (Ambush effect)'),
    'descriptionbattleBack' => clienttranslate(
      '${actplayer} must retreat the unit ${min} hex(es) ${desc} (Battle back effect)'
    ),
    'descriptionmyturnbattleBack' => clienttranslate(
      '${you} must retreat the unit ${min} hex(es) ${desc} (Battle back effect)'
    ),
    'descriptionbattleBackskippable' => clienttranslate(
      '${actplayer} may retreat the unit up to ${max} hex(es) (Battle back effect)'
    ),
    'descriptionmyturnbattleBackskippable' => clienttranslate(
      '${you} may retreat the unit up to ${max} hex(es) (Battle back effect)'
    ),
    'type' => 'activeplayer',
    // 'args' => 'argsAmbushResolve',
    // 'possibleactions' => ['actRetreat'],
    'args' => 'argsRetreatUnit',
    'action' => 'stRetreatUnit',
    'possibleactions' => ['actRetreatUnit', 'actRetreatUnitDone', 'actIgnore1Flag'],
    'transitions' => ['takeGround' => ST_ATTACK_THROW, 'retreat' => ST_AMBUSH_RESOLVE], // to default go to attack resolution
  ],

  ///////////////////////////////////
  //  _____          _   _
  // |_   _|_ _  ___| |_(_) ___
  //   | |/ _` |/ __| __| |/ __|
  //   | | (_| | (__| |_| | (__
  //   |_|\__,_|\___|\__|_|\___|
  //
  ///////////////////////////////////
  ST_DIG_IN => [
    'name' => 'digIn',
    'description' => '',
    'type' => 'game',
    'action' => 'stDigIn',
    'descriptionmyturn' => '',
    'transitions' => ['next' => ST_DRAW],
  ],

  // Behind ennemy lines
  ST_PRE_MOVE_AGAIN => [
    'name' => 'preMoveAgain',
    'description' => '',
    'type' => 'game',
    'action' => 'stMoveAgain',
    'descriptionmyturn' => '',
    'transitions' => ['next' => ST_MOVE_AGAIN],
  ],

  ST_MOVE_AGAIN => [
    'name' => 'moveUnits',
    'description' => clienttranslate('${actplayer} may move activated units again (behind enemy lines effect)'),
    'descriptionmyturn' => clienttranslate('${you} may move activated units again (behind enemy lines effect)'),
    'type' => 'activeplayer',
    'args' => 'argsMoveUnits',
    'action' => 'stMoveUnits',
    'possibleactions' => ['actRestart', 'actMoveUnit', 'actMoveUnitsDone', 'actExitUnit'],
    'transitions' => ['moveUnits' => ST_MOVE_AGAIN, 'attackUnits' => ST_DRAW],
  ],

  // Finest Hour
  ST_FINEST_HOUR_ROLL => [
    'name' => 'finestHourRoll',
    'description' => '',
    'type' => 'game',
    'action' => 'stFinestHourRoll',
    'descriptionmyturn' => '',
    'transitions' => ['selectUnits' => ST_FINEST_HOUR_ORDER],
  ],

  ST_FINEST_HOUR_ORDER => [
    'name' => 'orderUnitsFinestHour',
    'description' => clienttranslate('${actplayer} may order ${unitDesc}'),
    'descriptionmyturn' => clienttranslate('${you} may order ${unitDesc}'),
    'type' => 'activeplayer',
    'args' => 'argsOrderUnitsFinestHour',
    'action' => 'stOrderUnitsFinestHour',
    'possibleactions' => ['actOrderUnitsFinestHour'],
    'transitions' => ['moveUnits' => ST_MOVE_UNITS],
  ],

  // Air power
  ST_AIRPOWER_TARGET => [
    'name' => 'targetAirPower',
    'description' => clienttranslate('${actplayer} may target 4 or fewer enemy units'),
    'descriptionmyturn' => clienttranslate('${you} may target 4 or fewer enemy units'),
    'type' => 'activeplayer',
    'args' => 'argsTargetAirPower',
    'possibleactions' => ['actRestart', 'actTargetAirPower'],
    'transitions' => ['attack' => ST_ATTACK_THROW],
  ],

  ST_BARRAGE_TARGET => [
    'name' => 'targetBarrage',
    'description' => clienttranslate('${actplayer} may target 1 enemy unit'),
    'descriptionmyturn' => clienttranslate('${you} may target 1 enemy unit'),
    'type' => 'activeplayer',
    'args' => 'argsTargetBarrage',
    'possibleactions' => ['actRestart', 'actTargetBarrage'],
    'transitions' => ['attack' => ST_ATTACK_THROW],
  ],

  ST_MEDICS_TARGET => [
    'name' => 'targetMedics',
    'description' => clienttranslate('${actplayer} may heal ${nTitle} unit(s)'),
    'descriptionmyturn' => clienttranslate('${you} may heal  ${nTitle} unit(s)'),
    'type' => 'activeplayer',
    'args' => 'argsTargetMedics',
    'action' => 'stTargetMedics',
    'possibleactions' => ['actRestart', 'actTargetMedics'],
    'transitions' => ['move' => ST_MOVE_UNITS, 'draw' => ST_DRAW],
  ],

  ST_COUNTER_ATTACK => [
    'name' => 'counterAttack',
    'description' => '',
    'descriptionmyturn' => '',
    'type' => 'activeplayer',
    'action' => 'stCounterAttack',
    'transitions' => [
      'selectUnits' => ST_ORDER_UNITS,
      'finestHour' => ST_FINEST_HOUR_ROLL,
      'airpower' => ST_AIRPOWER_TARGET,
      'barrage' => ST_BARRAGE_TARGET,
      'medics' => ST_MEDICS_TARGET,
      'draw' => ST_CONFIRM_TURN,
      'counterAttack' => ST_COUNTER_ATTACK,
    ],
  ],

  // Medics BT
  ST_MEDICS_BT_ROLL => [
    'name' => 'medicsBTRoll',
    'description' => '',
    'type' => 'game',
    'action' => 'stMedicsBTRoll',
    'descriptionmyturn' => '',
    'transitions' => ['healUnits' => ST_MEDICS_BT_HEAL],
  ],

  ST_MEDICS_BT_HEAL => [
    'name' => 'medicsBTHeal',
    'description' => clienttranslate('${actplayer} may heal ${unitDesc}'),
    'descriptionmyturn' => clienttranslate('${you} may heal ${unitDesc}'),
    'type' => 'activeplayer',
    'args' => 'argsMedicsBTHeal',
    'possibleactions' => ['actMedicsBTHeal'],
    'transitions' => ['move' => ST_MOVE_UNITS, 'draw' => ST_DRAW],
  ],

  /////////////////////////////////////////////
  //   ___                 _               _
  //  / _ \__   _____ _ __| | ___  _ __ __| |
  // | | | \ \ / / _ \ '__| |/ _ \| '__/ _` |
  // | |_| |\ V /  __/ |  | | (_) | | | (_| |
  //  \___/  \_/ \___|_|  |_|\___/|_|  \__,_|
  //
  /////////////////////////////////////////////

  ST_OVERLORD_PLAY_CARD => [
    'name' => 'playCard',
    'description' => clienttranslate('Players must play a card'),
    'descriptionmyturn' => clienttranslate('${you} must play a card'),
    'type' => 'multipleactiveplayer',
    'args' => 'argsOverlordPlayCard',
    'possibleactions' => ['actPlayCard'],
    'transitions' => ['moveUnits' => ST_ORDER_UNITS],
  ],

  ST_OVERLORD_SELECT_UNIT => [
    'name' => 'selectUnits',
    'description' => clienttranslate('${actplayer} must select units in sections ${section}'),
    'descriptionmyturn' => clienttranslate('${you} must select units in sections ${section}'),
    'type' => 'multipleactiveplayer',
    'args' => 'argsOverlordSelectUnits',
    'possibleactions' => ['actSelectUnits'],
    'transitions' => ['moveUnits' => ST_MOVE_UNITS],
  ],

  ST_OVERLORD_MOVE_UNIT => [
    'name' => 'moveUnits',
    'description' => clienttranslate('${actplayer} must move selected units'),
    'descriptionmyturn' => clienttranslate('${you} must move selected units'),
    'type' => 'multipleactiveplayer',
    'args' => 'argsOverlordMoveUnits',
    'possibleactions' => ['actMoveUnits'],
    'transitions' => ['moveUnits' => ST_MOVE_UNITS],
  ],

  ST_OVERLORD_ATTACK => [
    'name' => 'attackUnit',
    'description' => clienttranslate('${actplayer} must select an unit and its target'),
    'descriptionmyturn' => clienttranslate('${you} must select an unit and its target'),
    'type' => 'multipleactiveplayer',
    'args' => 'argsOverlordAttackUnit',
    'possibleactions' => ['actAttackUnit'],
    'transitions' => ['ambush' => ST_OPPONENT_AMBUSH, 'attack' => ST_ATTACK_THROW], // attack if not close assault
  ],

  // Generic state to change player
  ST_CHANGE_ACTIVE_PLAYER => [
    'name' => 'changeActivePlayer',
    'description' => '',
    'descriptionmyturn' => '',
    'type' => 'game',
    'action' => 'stChangeActivePlayer',
  ],

  // Final state.
  // Please do not modify (and do not overload action/args methods).
  ST_END_OF_GAME => [
    'name' => 'endOfGame',
    'descriptionmyturn' => '',
    'description' => '',
    'type' => 'game',
    'action' => 'stEndOfGame',
    'transitions' => ['' => ST_END_GAME],
  ],

  // Final state.
  // Please do not modify (and do not overload action/args methods).
  ST_END_GAME => [
    'name' => 'gameEnd',
    'description' => clienttranslate('End of game'),
    'type' => 'manager',
    'action' => 'stGameEnd',
    'args' => 'argGameEnd',
  ],
];
