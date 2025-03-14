<?php
namespace M44\Core;
use M44\Managers\Players;
use M44\Helpers\Utils;
use M44\Core\Globals;
use M44\Core\Stats;
use M44\Managers\Cards;
use M44\Managers\Units;
use M44\Managers\Teams;

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

  public static function smallRefreshInterface($data)
  {
    self::notifyAll('smallRefreshInterface', '', $data);
  }

  public static function smallRefreshHand($player)
  {
    self::notify($player, 'smallRefreshHand', '', [
      'playerDatas' => $player->jsonSerialize($player->getId()),
    ]);
  }

  public static function visibility($increase)
  {
    if($increase > 0) {
      self::notifyAll('updateVisibility', clienttranslate('Night visibility increase by ${star}'), [
        'star' => $increase,
      ]);
    } else {
      self::notifyAll('updateVisibility', clienttranslate('Night visibility decrease by ${abs_star}'), [
        'star' => $increase,
        'abs_star' => -$increase,
      ]);
    }
  }

  public static function increaseTurn($turn) 
  {
    self::notifyAll('updateTurn', '', ['turn' => $turn]);
  }

  public static function proposeScenario($player, $scenario, $counter = false)
  {
    if ($counter === true) {
      $msg = clienttranslate('${player_name} counter-proposes ${scenario_name}');
    } else {
      $msg = clienttranslate('${player_name} proposes ${scenario_name}');
    }
    self::notifyAll('proposeScenario', $msg, [
      'player' => $player,
      'scenario' => $scenario,
      'scenario_name' => $scenario['text']['en']['name'] ?? ($scenario['text']['fr']['name'] ?? 'not defined'),
    ]);
  }

  public static function clearTurn($player, $notifIds)
  {
    self::notifyAll('clearTurn', clienttranslate('${player_name} restart their turn'), [
      'player' => $player,
      'notifIds' => $notifIds,
    ]);
  }

  public static function updateStats()
  {
    self::notifyAll('updateStats', '', ['stats' => Stats::getUiData()]);
  }

  public static function throwAttack($player, $unit, $nDice, $oppUnit)
  {
    if (is_null($oppUnit)) {
      throw new \BgaVisibleSystemException(
        'Should not happen. Please create a bug report at this exact point in the game with details on what you were trying to do'
      );
    }

    $data = [
      'player' => $player,
      'coordTarget' => $oppUnit->getPos(),
      'oppUnitId' => $oppUnit->getId(),
      'unit_desc' => self::computeUnitsDesc([$oppUnit]),
    ];

    if (is_null($unit)) {
      self::notifyAll('throwAttack', clienttranslate('${player_name} attacks ${unit_desc} (${coordTarget})'), $data);
    } else {
      $data['unitId'] = $unit->getId();
      $data['unit2_desc'] = self::computeUnitsDesc([$unit]);
      $data['coordSource'] = $unit->getPos();
      self::notifyAll(
        'throwAttack',
        clienttranslate(
          '${player_name} attacks ${unit_desc} (${coordTarget}) with their ${unit2_desc} at ${coordSource}'
        ),
        $data
      );
    }
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
    // COUNTER ATTACK
    if ($card->getType() == \CARD_COUNTER_ATTACK && !is_null($card->getCopiedCard())) {
      $str = $card->getCopiedCard()->getNotifString();
      $copiedCard = Cards::getInstance($card->getExtraDatas('copiedCardType'));
      //$str = $copiedCard->getNotifString();  

      if (is_null($str)) {
        self::notifyAll('playCard', clienttranslate('${player_name} plays ${card_name} as ${copied_card_name}'), [
          'i18n' => ['copied_card_name'],
          'player' => $player,
          'card' => $card,
          'copied_card_name' => $copiedCard->getName(),
        ]);
      } else {
        self::notifyAll(
          'playCard',
          clienttranslate('${player_name} plays ${card_name} as ${copied_card_name} on ${flank}'),
          [
            'i18n' => ['copied_card_name', 'flank'],
            'player' => $player,
            'card' => $card,
            'copied_card_name' => $copiedCard->getName(),
            'flank' => $str,
          ]
        );
      }
    }
    // NORMAL CASE
    else {
      $str = $card->getNotifString();
      if (is_null($str)) {
        self::notifyAll('playCard', clienttranslate('${player_name} plays ${card_name}'), [
          'player' => $player,
          'card' => $card,
        ]);
      } else {
        self::notifyAll('playCard', clienttranslate('${player_name} plays ${card_name} on ${flank}'), [
          'i18n' => ['flank'],
          'player' => $player,
          'card' => $card,
          'flank' => $str,
        ]);
      }
    }
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

  public static function moveUnitNoMsg($player, $unit, $coordSource, $coordTarget)
  {
    self::notifyAll(
      'moveUnit',
      '',
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

  public static function moveUnitFromReserve($player, $unit, $coordSource, $coordTarget)
  {
    self::notifyAll(
      'moveUnitFromReserve',
      clienttranslate('${player_name} moves ${unit_desc} (From reserve staging area to ${coordTarget})'),
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
        'fromX' => $coordSource['x'],
        'fromY' => $coordSource['y'],
      ]
    );
  }

  public static function takeGround($player, $unitId, $x, $y, $coordSource)
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
        'fromX' => $coordSource['x'],
        'fromY' => $coordSource['y'],
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

  public static function discardCard($player, $card, $decHandCounter = true)
  {
    self::notifyAll('discardCard', clienttranslate('${player_name} discards ${card_name}'), [
      'player' => $player,
      'card' => $card,
      'handCounter' => $decHandCounter ? -1 : 0,
    ]);
  }

  public static function discardItalianHighCommand($player, $card)
  {
    self::notifyAll(
      'discardCardItalianHighCommand',
      clienttranslate('${player_name} discards ${card_name} due to Italian High command rule'),
      [
        'player' => $player,
        'card' => $card,
      ]
    );
  }

  public static function discardHQCapture($player, $card)
  {
    self::notifyAll(
      'discardCardItalianHighCommand',
      clienttranslate('${player_name} discards ${card_name} due to capture of HQ'),
      [
        'player' => $player,
        'card' => $card,
      ]
    );
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
    self::updateStats();
  }

  public static function removeMedals($teamId, $medalIds, $medal)
  {
    self::notifyAll('removeMedals', clienttranslate('${team_name} loses ${nb} medal(s)'), [
      'teamId' => $teamId,
      'nb' => count($medalIds),
      'medalIds' => $medalIds,
      'boardMedalId' => $medal['id'],
    ]);
    self::updateStats();
  }

  public static function scoreSectionMedals($teamId, $medals)
  {
    self::notifyAll('scoreMedals', clienttranslate('${team_name} scores ${nb} medal(s) for empty section'), [
      'teamId' => $teamId,
      'nb' => $medals->count(),
      'medals' => $medals->toArray(),
    ]);
    self::updateStats();
  }

  public static function removeSectionMedals($teamId, $medalIds)
  {
    self::notifyAll('removeSectionMedals', clienttranslate('${team_name} loses ${nb} medal(s)'), [
      'teamId' => $teamId,
      'nb' => count($medalIds),
      'medalIds' => $medalIds,
    ]);
    self::updateStats();
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
    self::updateStats();
  }

  public static function exitUnit($player, $unit)
  {
    self::notifyAll('exitUnit', clienttranslate('${player_name} exits 1 unit'), [
      'player' => $player,
      'unitId' => $unit->getId(),
      'cell' => $unit->getPos(),
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
    self::updateStats();

    self::notifyAll('message', clienttranslate('${team_name} wins round n°${nb}'), [
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

  public static function trainReinforcement($player, $unit)
  {
    self::notifyAll('trainReinforcement', \clienttranslate('${player_name} deployed 1 unit in ${coordSource}'), [
      'player' => $player,
      'unit' => $unit,
      'coordSource' => $unit->getPos(),
    ]);
  }

  public static function ReserveUnitDeployement($player, $unit, $onStageArea = false)
  { $message = $onStageArea ? 
    self::notifyAll('reserveUnitsDeployement', \clienttranslate('${player_name} deployed 1 ${unit_name} on reserve staging area'), [
      'player' => $player,
      'unit' => $unit,
      'unit_name' => $unit->getName(),
      'coordSource' => $unit->getPos(),
      'team' => $player->getTeam(),
      'stage_area' => $onStageArea,
      'teams' => Teams::getAll(),
    ])
    : self::notifyAll('reserveUnitsDeployement', \clienttranslate('${player_name} deployed 1 ${unit_name} in ${coordSource} from reserve depot'), [
      'player' => $player,
      'unit' => $unit,
      'unit_name' => $unit->getName(),
      'coordSource' => $unit->getPos(),
      'team' => $player->getTeam(),
      'stage_area' => $onStageArea,
      'teams' => Teams::getAll(),
    ]);
  }

  public static function clearEndReserveDeployement($playerid) {
    self::notify($playerid, 'clearEndReserveDeployement', '', NULL);
  }

  public static function ArmorBreakthroughDeployement($player, $unit)
  { 
  self::notifyAll('armorBreakthroughDeployement', \clienttranslate('${player_name} deployed 1 ${unit_name} in ${coordSource} according to Armor Breakthrough rule'), [
      'player' => $player,
      'unit' => $unit,
      'unit_name' => $unit->getName(),
      'coordSource' => $unit->getPos(),
      'team' => $player->getTeam(),
      'teams' => Teams::getAll(),
    ]);
  }


  public static function replenishWinnerReserveTokens($team, $nbAddedTokens) {
  $player = $team->getCommander();
    self::notifyAll('replenishWinnerReserveTokens', 
    \clienttranslate('As winner, ${player_name} received back ${nbAddedTokens} reserve token from staging area'),
    [
      'player' => $player,
      'team' => $team,
      'nbAddedTokens' => $nbAddedTokens,
    ]);
  }

  public static function addAirpowerToken($player) {
    self::notifyAll('addAirPowerToken', \clienttranslate('${player_name} received 1 Air Power token'), [
      'player' => $player,
      'team' => $player->getTeam(),
      'teams' => Teams::getAll(),
    ]);
  }

  public static function removeAirpowerToken($player) {
    self::notifyAll('removeAirPowerToken', \clienttranslate('${player_name} used 1 Air Power token instead of playing a card'), [
      'player' => $player,
      'team' => $player->getTeam(),
      'teams' => Teams::getAll(),
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

  public static function removeStarToken($id, $x, $y)
  {
    self::notifyAll('removeStarToken', '', [
      'id' => $id,
      'x' => $x,
      'y' => $y,
    ]);
  }

  public static function removeUnitfromBoard($id)
  {
    self::notifyAll('removeUnit', 
    clienttranslate('${unit_name} removed from board'), [
      'id' => $id,
      'unit_name' => Units::get($id)->getName()
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
  }

  protected static function computeUnitsDesc($units)
  {
    $type = [
      \INFANTRY => 0,
      \ARMOR => 0,
      \ARTILLERY => 0,
      \DESTROYER => 0,
      \LOCOMOTIVE => 0,
      \WAGON => 0,
    ];
    $names = [
      \INFANTRY => \clienttranslate('Infantry'),
      \ARMOR => \clienttranslate('Armor'),
      \ARTILLERY => \clienttranslate('Artillery'),
      \DESTROYER => \clienttranslate('Destroyer'),
      \LOCOMOTIVE => \clienttranslate('Locomotive'),
      \WAGON => \clienttranslate('Wagon'),
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
