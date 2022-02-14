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
    'transitions' => ['selectUnits' => ST_ORDER_UNITS],
  ],

  ST_ORDER_UNITS => [
    'name' => 'orderUnits',
    'description' => clienttranslate('${actplayer} must order ${n} unit(s) ${desc}'),
    'descriptionmyturn' => clienttranslate('${you} must order ${n} unit(s) ${desc}'),
    'type' => 'activeplayer',
    'args' => 'argsOrderUnits',
    'possibleactions' => ['actOrderUnits'],
    'transitions' => ['moveUnits' => ST_MOVE_UNITS],
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
    'possibleactions' => ['actAttackUnit', 'actAttackUnitsDone'],
    'transitions' => ['ambush' => ST_PRE_AMBUSH, 'attack' => ST_ATTACK_THROW, 'draw' => ST_DRAW], // attack if not close assault
  ],

  ST_PRE_AMBUSH => [
    'name' => 'preAmbush',
    'description' => '',
    'descriptionmyturn' => '',
    'type' => 'game',
    'action' => 'stChangePlayer',
    'transitions' => ['next' => ST_OPPONENT_AMBUSH],
  ],

  ST_OPPONENT_AMBUSH => [
    'name' => 'opponentAmbush',
    'description' => clienttranslate('${actplayer} can react to the attack'),
    'descriptionmyturn' => clienttranslate('${you} can react to the attack'),
    'type' => 'activeplayer',
    'args' => 'argsOpponentAmbush',
    'possibleactions' => ['actAmbush', 'actPassAmbush'],
    'transitions' => ['pass' => ST_POST_AMBUSH, 'ambush' => ST_AMBUSH_ATTACK],
  ],

  ST_AMBUSH_ATTACK => [
    'name' => 'ambushAttack',
    'description' => '',
    'descriptionmyturn' => '',
    'type' => 'game',
    'action' => 'stAttackThrow',
    'transitions' => ['next' => ST_AMBUSH_RESOLVE, 'endAmbush' => ST_POST_AMBUSH],
  ],

  ST_AMBUSH_RESOLVE => [
    'name' => 'attackResolve',
    'description' => clienttranslate('${actplayer} must retreat the unit'),
    'descriptionmyturn' => clienttranslate('${you} must retreat the unit'),
    'type' => 'activeplayer',
    'args' => 'argsAttackResolve',
    'possibleactions' => ['actRetreat'],
    'transitions' => ['endRound' => ST_POST_AMBUSH],
  ],

  ST_POST_AMBUSH => [
    'name' => 'postAmbush',
    'description' => '',
    'descriptionmyturn' => '',
    'type' => 'game',
    'action' => 'stChangePlayer',
    'transitions' => ['next' => ST_ATTACK_THROW],
  ],

  ST_ATTACK_THROW => [
    'name' => 'attackThrow',
    'description' => '',
    'descriptionmyturn' => '',
    'type' => 'game',
    'action' => 'stAttackThrow', // TODO: possible that attack not possible anymore
    'transitions' => ['next' => ST_ATTACK_RESOLVE, 'draw' => ST_DRAW, 'attack' => ST_ATTACK],
  ],

  ST_ATTACK_RESOLVE => [
    'name' => 'attackResolve',
    'description' => clienttranslate('${actplayer} must retreat the unit'),
    'descriptionmyturn' => clienttranslate('${you} must retreat the unit'),
    'type' => 'multipleactiveplayer',
    'args' => 'argsAttackResolve',
    'possibleactions' => ['actRetreat'],
    'transitions' => ['draw' => ST_DRAW, 'attack' => ST_ATTACK],
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
    'transitions' => ['ambush' => ST_PRE_AMBUSH, 'attack' => ST_ATTACK_THROW], // attack if not close assault
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
