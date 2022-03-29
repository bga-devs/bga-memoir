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

  public static function refreshInterface($data)
  {
    self::notifyAll('refreshInterface', '', $data);
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

  public static function orderUnits($player, $units, $unitsOnTheMove = null)
  {
    if (is_null($unitsOnTheMove) || $unitsOnTheMove->empty()) {
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

  public static function moveUnit($player, $unit, $coordSource, $coordTarget)
  {
    self::notifyAll(
      'moveUnit',
      clienttranslate('${player_name} moves ${unit_desc} (${coordSource} to ${coordTarget})'),
      [
        'player' => $player,
        'unitId' => $unit->getId(),
        'unit_desc' => self::computeUnitsDesc([$unit]),
        'coordSource' => $coordSource,
        'coordTarget' => $coordTarget,
        'x' => $coordTarget['x'],
        'y' => $coordTarget['y'],
        'fromX' => $coordSource['x'],
        'fromY' => $coordSource['y'],
      ]
    );
  }

  public static function retreatUnit($player, $unit, $coordSource, $coordTarget)
  {
    self::notifyAll(
      'moveUnit',
      clienttranslate('${player_name} retreats ${unit_desc} (${coordSource} to ${coordTarget})'),
      [
        'player' => $player,
        'unitId' => $unit->getId(),
        'unit_desc' => self::computeUnitsDesc([$unit]),
        'coordSource' => $coordSource,
        'coordTarget' => $coordTarget,
        'x' => $coordTarget['x'],
        'y' => $coordTarget['y'],
      ]
    );
  }

  public static function takeGround($player, $unitId, $x, $y)
  {
    self::notifyAll(
      'moveUnit',
      clienttranslate('${player_name} takes ground in ${coordTarget} after successfull attack'),
      [
        'player' => $player,
        'unitId' => $unitId,
        'x' => $x,
        'y' => $y,
        'coordTarget' => ['x' => $x, 'y' => $y],
      ]
    );
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

  public static function discardCards($player, $cards)
  {
    if ($cards->count() == 1) {
      self::discardCard($player, $cards->first());
    } else {
      die('No handled in front');
      self::notifyAll('discardCards', clienttranslate('${player_name} discards ${nb} card(s)'), [
        'player' => $player,
        'nb' => count($cards),
        'cards' => $cards,
      ]);
    }
  }

  public static function scoreMedals($teamId, $medals, $cell = null)
  {
    self::notifyAll('scoreMedals', clienttranslate('${team_name} scores ${nb} medal(s)'), [
      'teamId' => $teamId,
      'nb' => $medals->count(),
      'medals' => $medals->toArray(),
      'cell' => $cell,
    ]);
  }

  public static function removeMedals($teamId, $medalIds, $medal)
  {
    self::notifyAll('removeMedals', clienttranslate('${team_name} loses ${nb} medal(s)'), [
      'teamId' => $teamId,
      'nb' => count($medalIds),
      'medalIds' => $medalIds,
      'boardMedalId' => $medal['id'],
    ]);
  }

  public static function addToken($token)
  {
    self::notifyAll('addToken', '', ['token' => $token]);
  }

  public static function removeToken($token)
  {
    self::notifyAll('removeToken', '', ['token' => $token]);
  }

  public static function clearUnitsStatus()
  {
    self::notifyAll('clearUnitsStatus', '', []);
  }

  public static function takeDamage($player, $oppUnit, $hits, $cantRetreat)
  {
    $msg = $cantRetreat
      ? clienttranslate('${player_name}\'s unit (in ${coordSource}) takes ${hits} damage(s) because retreat is blocked')
      : clienttranslate('${player_name}\'s unit (in ${coordSource}) takes ${hits} damage(s)');
    self::notifyAll('takeDamage', $msg, [
      'player' => $player,
      'unitId' => $oppUnit->getId(),
      'cell' => $oppUnit->getPos(),
      'hits' => $hits,
      'coordSource' => $oppUnit->getPos(),
    ]);
  }

  public static function miss($oppUnit)
  {
    self::notifyAll('miss', '', [
      'unitId' => $oppUnit->getId(),
    ]);
  }

  public static function healUnit($player, $nb, $unit)
  {
    self::notifyAll(
      'healUnit',
      clienttranslate('${player_name} heals ${nb} damage(s) to ${unit_desc} (in ${coordSource})'),
      [
        'player' => $player,
        'unitId' => $unit->getId(),
        'nb' => $nb,
        'unit_desc' => self::computeUnitsDesc([$unit]),
        'coordSource' => $unit->getPos(),
      ]
    );
  }

  public static function removeTerrain($terrain)
  {
    self::notifyAll('removeTerrain', clienttranslate('${obstacle} is removed in ${coordSource}'), [
      'terrainId' => $terrain->getId(),
      'cell' => $terrain->getPos(),
      'obstacle' => $terrain->getName(),
      'coordSource' => $terrain->getPos(),
      'i18n' => ['obstacle'],
    ]);
  }

  public static function addTerrain($player, $terrain, $msg)
  {
    self::notifyAll('addTerrain', $msg, [
      'player' => $player,
      'terrain' => $terrain,
      'coordSource' => $terrain->getPos(),
    ]);
  }

  public static function reshuffle($nDeck)
  {
    self::notifyAll('reshuffle', '', [
      'nDeck' => $nDeck,
    ]);
  }

  public static function winRound($team, $round)
  {
    self::notifyAll('message', clienttranslate('${team_name} wins ${nb} round'), [
      'teamId' => $team->getId(),
      'nb' => $round,
    ]);
  }

  public static function airDrop($player, $unit)
  {
    self::notifyAll('airDrop', \clienttranslate('${player_name} successfully air drops a unit in ${coordSource}'), [
      'player' => $player,
      'unit' => $unit,
      'coordSource' => $unit->getPos(),
    ]);
  }

  public static function revealMinefield($player, $terrainId, $cell, $value)
  {
    $msg =
      $value == 0
        ? \clienttranslate('${player_name} reveals Minefield at ${coordSource}: it\'s a decoy!')
        : \clienttranslate('${player_name} reveals Minefield at ${coordSource}: it\'s a ${value} force Minefield');

    self::notifyAll('revealMinefield', $msg, [
      'player' => $player,
      'coordSource' => $cell,
      'cell' => $cell,
      'terrainId' => $terrainId,
      'value' => $value,
    ]);
  }

  public static function commissarCard($player, $card)
  {
    self::notifyAll('commissarCard', clienttranslate('${player_name} puts 1 card under the commissar token'), [
      'player' => $player,
    ]);
    self::notify($player, 'pCommissarCard', '', ['card' => $card]);
  }

  public static function revealCommissarCard($player, $card)
  {
    self::notifyAll(
      'revealCommissarCard',
      clienttranslate('${player_name} plays ${card_name} from its commissar token'),
      [
        'player' => $player,
        'card' => $card,
      ]
    );
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

    if (isset($args['teamId'])) {
      $teamNames = [
        ALLIES => \clienttranslate('Allies'),
        AXIS => \clienttranslate('Axis'),
      ];
      $args['team_name'] = $teamNames[$args['teamId']];
      $args['i18n'][] = 'team_name';
    }

    if (isset($args['coordSource'])) {
      $args['coordSource'] = Utils::computeCoords($args['coordSource']);
    }

    if (isset($args['coordTarget'])) {
      $args['coordTarget'] = Utils::computeCoords($args['coordTarget']);
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
