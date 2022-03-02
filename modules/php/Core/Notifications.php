<?php
namespace M44\Core;
use M44\Managers\Players;
use M44\Helpers\Utils;
use M44\Core\Globals;

class Notifications
{
  /*************************
   **** GENERIC METHODS ****
   *************************/
  protected static function notifyAll($name, $msg, $data)
  {
    self::updateArgs($data);
    Game::get()->notifyAllPlayers($name, $msg, $data);
  }

  protected static function notify($player, $name, $msg, $data)
  {
    $pId = is_int($player) ? $player : $player->getId();
    self::updateArgs($data);
    Game::get()->notifyPlayer($pId, $name, $msg, $data);
  }

  public static function message($txt, $args = [])
  {
    self::notifyAll('message', $txt, $args);
  }

  public static function messageTo($player, $txt, $args = [])
  {
    $pId = is_int($player) ? $player : $player->getId();
    self::notify($pId, 'message', $txt, $args);
  }

  public static function rollDice($player, $nDice, $results, $cell)
  {
    $faces = [
      DICE_INFANTRY => clienttranslate('Infantry'),
      DICE_ARMOR => clienttranslate('Armor'),
      DICE_GRENADE => clienttranslate('Grenade'),
      DICE_FLAG => clienttranslate('Flag'),
      DICE_STAR => clienttranslate('Star'),
    ];
    $diceLogs = [];
    $diceArgs = [];
    foreach ($results as $i => $r) {
      $name = 'dice_' . $i;
      $diceLogs[] = '${' . $name . '}';
      $diceArgs[$name] = [
        'log' => '${dice_face}',
        'args' => [
          'i18n' => ['dice_face'],
          'dice_face' => $faces[$r],
          'dice_result' => $r,
        ],
      ];
      $diceArgs['i18n'][] = $name;
    }

    self::notifyAll('rollDice', clienttranslate('${player_name} rolls ${dice_result}'), [
      'i18n' => ['dice_result'],
      'player' => $player,
      'cell' => $cell,
      'results' => $results,
      'dice_result' => [
        'log' => join(' ', $diceLogs),
        'args' => $diceArgs,
      ],
    ]);
  }

  public static function ambush($player, $card)
  {
    self::notifyAll('playCard', clienttranslate('${player_name} plays ${card_name} in reaction to Close assault'), [
      'player' => $player,
      'card' => $card,
    ]);
  }

  public static function playCard($player, $card)
  {
    self::notifyAll('playCard', clienttranslate('${player_name} plays ${card_name}'), [
      'player' => $player,
      'card' => $card,
    ]);
  }

  public static function orderUnits($player, $units, $unitsOnTheMove)
  {
    if ($unitsOnTheMove->empty()) {
      self::notifyAll('activateUnits', \clienttranslate('${player_name} issues orders to ${unit_desc}'), [
        'player' => $player,
        'unit_desc' => self::computeUnitsDesc($units),
        'unitIds' => $units->getIds(),
      ]);
    } else {
      self::notifyAll(
        'activateUnits',
        \clienttranslate('${player_name} issues orders to ${unit_desc} and orders ${unit2_desc} on the move'),
        [
          'player' => $player,
          'unit_desc' => self::computeUnitsDesc($units),
          'unitIds' => $units->getIds(),
          'unit2_desc' => self::computeUnitsDesc($unitsOnTheMove),
          'unitOnTheMoveIds' => $unitsOnTheMove->getIds(),
        ]
      );
    }
  }

  public static function moveUnit($player, $unitId, $x, $y)
  {
    self::notifyAll('moveUnit', '', [
      'player' => $player,
      'unitId' => $unitId,
      'x' => $x,
      'y' => $y,
    ]);
  }

  public static function takeGround($player, $unitId, $x, $y)
  {
    self::notifyAll('moveUnit', clienttranslate('${player_name} takes ground after successfull attack'), [
      'player' => $player,
      'unitId' => $unitId,
      'x' => $x,
      'y' => $y,
    ]);
  }

  public static function drawCards($player, $cards, $silent = false)
  {
    $msg = $silent ? '' : clienttranslate('${player_name} draws ${nb} card(s)');
    self::notifyAll('drawCards', $msg, [
      'player' => $player,
      'nb' => $cards->count(),
    ]);
    self::notify($player, 'pDrawCards', '', ['cards' => $cards->toArray()]);
  }

  public static function drawCardsAndKeep($player, $cards, $nbKeep)
  {
    $msg = clienttranslate('${player_name} draws ${nb} cards and will keep ${nbKeep}');
    self::notifyAll('drawCardsAndKeep', $msg, [
      'player' => $player,
      'nb' => $cards->count(),
      'nbKeep' => $nbKeep,
    ]);
    self::notify($player, 'pDrawCards', '', ['cards' => $cards->toArray()]);
  }

  public static function discardCard($player, $card)
  {
    self::notifyAll('discardCard', clienttranslate('${player_name} discards ${card_name}'), [
      'player' => $player,
      'card' => $card,
    ]);
  }

  public static function discardDrawCards($player, $cards)
  {
    self::notifyAll('discardCard', clienttranslate('${player_name} discards ${nb} card(s)'), [
      'player' => $player,
      'nb' => count($cards),
    ]);
  }

  public static function scoreMedal($player, $nb)
  {
    self::notifyAll('scoreMedal', clienttranslate('${player_name} scores ${nb} medal(s)'), [
      'player' => $player,
      'nb' => $nb,
    ]);
  }

  /*
  public static function discard($player, $cards, $used = true)
  {
    if ($used) {
      $msg = clienttranslate('${player_name} discards used cards');
    } else {
      $msg = clienttranslate('${player_name} discards ${nb} cards');
    }

    self::notifyAll('discard', $msg, [
      'player' => $player,
      'nb' => count($cards),
      'cards' => $cards,
    ]);
  }
*/

  public static function clearUnitsStatus()
  {
    self::notifyAll('clearUnitsStatus', '', []);
  }

  public static function takeDamage($player, $oppUnit, $hits, $cantRetreat)
  {
    $msg = $cantRetreat
      ? clienttranslate('${player_name}\'s unit takes ${hits} damage because retreat is blocked')
      : clienttranslate('${player_name}\'s unit takes ${hits} damage');
    self::notifyAll('takeDamage', $msg, [
      'player' => $player,
      'unitId' => $oppUnit->getId(),
      'cell' => $oppUnit->getPos(),
      'hits' => $hits,
    ]);
  }

  public static function removeObstacle($terrain)
  {
    self::notifyAll('removeObstacle', '', [
      'terrainId' => $terrain->getId(),
    ]);
  }

  /*********************
   **** UPDATE ARGS ****
   *********************/
  /*
   * Automatically adds some standard field about player and/or card
   */
  protected static function updateArgs(&$args)
  {
    if (isset($args['player'])) {
      $args['player_name'] = $args['player']->getName();
      $args['player_id'] = $args['player']->getId();
      unset($args['player']);
    }

    if (isset($args['card'])) {
      $args['card_name'] = $args['card']->getName();
      $args['i18n'][] = 'card_name';
    }

    // if (isset($args['task'])) {
    //   $c = $args['task'];
    //   $args['task_desc'] = $c->getText();
    //   $args['i18n'][] = 'task_desc';
    //
    //   if (isset($args['player_id'])) {
    //     $args['task'] = $args['task']->jsonSerialize($args['task']->getPId() == $args['player_id']);
    //   }
    // }
  }

  protected static function computeUnitsDesc($units)
  {
    $type = [
      \INFANTRY => 0,
      \ARMOR => 0,
      \ARTILLERY => 0,
    ];
    $names = [
      \INFANTRY => \clienttranslate('Infantry(s)'),
      \ARMOR => \clienttranslate('Armor(s)'),
      \ARTILLERY => \clienttranslate('Artillery(s)'),
    ];

    foreach ($units as $unit) {
      $type[$unit->getType()]++;
    }

    $logs = [];
    $args = [];
    foreach ($type as $t => $n) {
      if ($n > 0) {
        $name = 'unit_' . $t . '_name';
        $logs[] = '${unit_' . $t . '_number} ${' . $name . '}';
        $args["unit_${t}_number"] = $n;
        $args[$name] = $names[$t];
        $args['i18n'][] = $name;
      }
    }

    return [
      'log' => join(',', $logs),
      'args' => $args,
    ];
  }
}

?>
