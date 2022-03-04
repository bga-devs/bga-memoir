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
    'transitions' => ['' => ST_M44],
  ],

  ST_M44 => [
    'name' => 'playerTurn',
    'description' => clienttranslate('${actplayer} must play a card or pass'),
    'descriptionmyturn' => clienttranslate('${you} must play a card or pass'),
    'type' => 'activeplayer',
    'action' => 'stDummyState',
    'possibleactions' => ['actPlayCard', 'actPpass'],
    'transitions' => ['done' => ST_M44],
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
    'transitions' => ['playCard' => ST_PLAY_CARD],
  ],

  ST_PLAY_CARD => [
    'name' => 'playCard',
    'description' => clienttranslate('${actplayer} must play a card'),
    'descriptionmyturn' => clienttranslate('${you} must play a card'),
    'type' => 'activeplayer',
    'args' => 'argsPlayCard',
    'possibleactions' => ['actPlayCard'],
    'transitions' => ['selectUnits' => ST_ORDER_UNITS, 'finestHour' => ST_FINEST_HOUR_ROLL],
  ],

  ST_ORDER_UNITS => [
    'name' => 'orderUnits',
    'description' => clienttranslate('${actplayer} may order ${nTitle} unit(s) ${desc}'),
    'descriptionmyturn' => clienttranslate('${you} may order ${nTitle} unit(s) ${desc}'),
    'type' => 'activeplayer',
    'args' => 'argsOrderUnits',
    'action' => 'stOrderUnits',
    'possibleactions' => ['actOrderUnits'],
    'transitions' => ['moveUnits' => ST_MOVE_UNITS, 'digIn' => ST_DIG_IN],
  ],

  ST_MOVE_UNITS => [
    'name' => 'moveUnits',
    'description' => clienttranslate('${actplayer} may move activated units'),
    'descriptionmyturn' => clienttranslate('${you} may move activated units'),
    'type' => 'activeplayer',
    'args' => 'argsMoveUnits',
    'action' => 'stMoveUnits',
    'possibleactions' => ['actMoveUnit', 'actMoveUnitsDone'],
    'transitions' => ['moveUnits' => ST_MOVE_UNITS, 'attackUnits' => ST_ATTACK],
  ],

  ST_ATTACK => [
    'name' => 'attackUnits',
    'description' => clienttranslate('${actplayer} may battle'),
    'descriptionmyturn' => clienttranslate('${you} may select the unit to battle with'),
    'type' => 'activeplayer',
    'action' => 'stAttackUnits',
    'args' => 'argsAttackUnit',
    'possibleactions' => ['actAttackUnit', 'actAttackUnitsDone', 'actRemoveWire'],
    'transitions' => [
      'ambush' => ST_OPPONENT_AMBUSH,
      'attack' => ST_ATTACK,
      'draw' => ST_DRAW,
      'moveAgain' => ST_PRE_MOVE_AGAIN,
    ],
  ],

  ST_ATTACK_THROW => [
    'name' => 'attackThrow',
    'description' => '',
    'descriptionmyturn' => '',
    'type' => 'game',
    'action' => 'stAttackThrow', // TODO: possible that attack not possible anymore
    'transitions' => ['retreat' => ST_ATTACK_RETREAT, 'nextAttack' => ST_ATTACK],
  ],

  ST_ATTACK_RETREAT => [
    'name' => 'attackRetreat',
    'description' => clienttranslate('${actplayer} must retreat the unit ${min} cell(s) ${desc}'),
    'descriptionmyturn' => clienttranslate('${you} must retreat the unit ${min} cell(s) ${desc}'),
    'descriptionskippable' => clienttranslate('${actplayer} may retreat the unit up to ${max} cell(s)'),
    'descriptionmyturnskippable' => clienttranslate('${you} may retreat the unit up to ${max} cell(s)'),
    'type' => 'activeplayer',
    'args' => 'argsRetreatUnit',
    'action' => 'stRetreatUnit',
    'possibleactions' => ['actRetreatUnit', 'actRetreatUnitDone'],
    'transitions' => [
      'retreat' => ST_ATTACK_RETREAT,
      'nextAttack' => ST_ATTACK,
      'takeGround' => ST_TAKING_GROUND,
    ],
  ],

  ST_DRAW => [
    'name' => 'drawCard',
    'description' => '',
    'type' => 'game',
    'action' => 'stDrawCard',
    'transitions' => ['endRound' => ST_END_ROUND, 'choice' => ST_DRAW_CHOICE],
  ],

  ST_DRAW_CHOICE => [
    'name' => 'drawChoice',
    'description' => clienttranslate('${actplayer} must choose which card to keep'),
    'descriptionmyturn' => clienttranslate('${you} must choose which card to keep'),
    'type' => 'activeplayer',
    'args' => 'argsDrawChoice',
    'possibleactions' => ['actChooseCard'],
    'transitions' => ['endRound' => ST_END_ROUND],
  ],

  ST_END_ROUND => [
    'name' => 'endRound',
    'description' => '',
    'descriptionmyturn' => '',
    'type' => 'game',
    'action' => 'stEndRound',
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
      'overrun' => ST_ARMOR_OVERRUN,
    ],
  ],

  ST_ARMOR_OVERRUN => [
    'name' => 'armorOverrun',
    'description' => clienttranslate('${actplayer} may attack an unit (Armor overrun)'),
    'descriptionmyturn' => clienttranslate('${you} may attack an unit (Armor overrun)'),
    'type' => 'activeplayer',
    'args' => 'argsArmorOverrun',
    'action' => 'stArmorOverrun',
    'possibleactions' => ['actAttackUnit', 'actNextAttack'],
    'transitions' => [
      'ambush' => ST_OPPONENT_AMBUSH,
      'next' => ST_ATTACK, // if player doesn't attack, no new taking ground
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
    'type' => 'activeplayer',
    'args' => 'argsOpponentAmbush',
    'action' => 'stAmbush',
    'possibleactions' => ['actAmbush', 'actPassAmbush'],
    'transitions' => ['pass' => ST_ATTACK_THROW, 'retreat' => ST_AMBUSH_RESOLVE],
  ],

  ST_AMBUSH_RESOLVE => [
    'name' => 'ambushResolve',
    'description' => clienttranslate('${actplayer} must retreat the unit (Ambush effect)'),
    'descriptionmyturn' => clienttranslate('${you} must retreat the unit (Ambush effect)'),
    'type' => 'activeplayer',
    // 'args' => 'argsAmbushResolve',
    // 'possibleactions' => ['actRetreat'],
    'args' => 'argsRetreatUnit',
    'action' => 'stRetreatUnit',
    'possibleactions' => ['actRetreatUnit', 'actRetreatUnitDone'],
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
    'description' => clienttranslate('${actplayer} may move activated units again (behind ennemy lines effect)'),
    'descriptionmyturn' => clienttranslate('${you} may move activated units again (behind ennemy lines effect)'),
    'type' => 'activeplayer',
    'args' => 'argsMoveUnits',
    'action' => 'stMoveUnits',
    'possibleactions' => ['actMoveUnit', 'actMoveUnitsDone'],
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
  ST_END_GAME => [
    'name' => 'gameEnd',
    'description' => clienttranslate('End of game'),
    'type' => 'manager',
    'action' => 'stGameEnd',
    'args' => 'argGameEnd',
  ],
];
