<?php

namespace M44\Managers;

use M44\Core\Globals;
use M44\Core\Game;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Helpers\Utils;
use M44\Board;
use M44\Scenario;

/**
 * Cards: id, value, color
 *  pId is stored as second part of the location, eg : table_2322020
 */
class Cards extends \M44\Helpers\Pieces
{
  protected static $table = 'cards';
  protected static $prefix = 'card_';
  protected static $customFields = ['type', 'value', 'extra_datas'];
  protected static $autoreshuffle = true;
  protected static $autoreshuffleCustom = ['deck' => 'discard'];
  protected static $autoreshuffleListener = ['obj' => 'M44\Managers\Cards', 'method' => 'reshuffleListener'];
  protected static function cast($row)
  {
    $locations = explode('_', $row['location']);
    $row['player_id'] = $locations[1] ?? null;
    return self::getInstance($row['type'], $row);
  }

  public static function getInstance($type, $row = null)
  {
    $mode = Scenario::getMode();
    $dirs = [
      STANDARD_DECK => 'Standard',
      BREAKTHROUGH_DECK => 'Breakthrough',
      OVERLORD_DECK => 'Overlord',
    ];

    $folder = $dirs[$mode] ?? 'Standard';
    $className = '\M44\Cards\\' . $folder . '\\' . \CARD_CLASSES[$type];
    return new $className($row);
  }

  public static function pickForLocation($nbr, $fromLocation, $toLocation, $state = 0, $deckReform = true)
  {
    $remaining = self::countInLocation('deck');
    if ($nbr > $remaining && !Globals::isDeckReshuffle()) {
      self::reshuffleListener();
      return null;
    }

    $p = parent::pickForLocation($nbr, $fromLocation, $toLocation, $state, $deckReform);

    return $p;
  }

  public static function reshuffleListener($loca = null)
  {
    if (Globals::getDefaultWinner() != null) {
      Notifications::message(clienttranslate('There are not more cards in the deck. Victory of ${side'), [
        'side' => Globals::getDefaultWinner(),
        'i18n' => ['side'],
      ]);
      Teams::get(Globals::getDefaultWinner())->addSuddenDeathMedals();

      Teams::checkVictory();
    }
  }

  //////////////////////////////////
  //////////////////////////////////
  //////////// GETTERS //////////////
  //////////////////////////////////
  //////////////////////////////////

  /**
   * getOfPlayer: return the cards in the hand of given player
   */
  public static function getOfPlayer($pId)
  {
    return self::getInLocation(['hand', $pId]);
  }

  /**
   * getInPlayOfPlayer: return the cards played by a player
   */
  public static function getInPlayOfPlayer($pId)
  {
    return self::getInLocation(['inplay', $pId])->first();
  }

  public static function getInPlayOfAll()
  {
    return self::getInLocation('inplay%');
  }

  /**
   * getByType
   */
  public static function getByType($type)
  {
    return self::getSelectQuery()
      ->where('type', $type)
      ->get();
  }

  /**
   * getIdByType
   * return Id of the first card of a type from the DB.
   * Usefull for AirPowerToken to create a virtual card an reuse an existing id without creating a line in the DB
   */
   public static function getIdByType($type) 
   {
    $cardsType = self::getByType($type)->toArray();
    return $cardsType[0]->getId();
   }

  //////////////////////////////////
  //////////////////////////////////
  ///////////// SETTERS ////////////
  //////////////////////////////////
  //////////////////////////////////

  public static function play($player, $cardId, $sectionId)
  {
    self::move($cardId, ['inplay', $player->getId()]);
    $last = Globals::getRawLastPlayedCards();
    // Fix 70821 : removed extra_datas when using Hill 317 is creating a JSON syntax error : RawLastPlayedCards[extra_datas] not used
    $last[$player->getId()] = Game::get()->getObjectFromDB("SELECT `card_id` AS id, `card_state` AS state, `card_location` AS location, type, value/*, extra_datas */ FROM `cards` WHERE `card_id` = $cardId");
    Globals::setRawLastPlayedCards($last);

    $last2 = Globals::getLastPlayedCards();
    $last2[$player->getId()] = $cardId;
    Globals::setLastPlayedCards($last2);

    self::get($cardId)->setExtraDatas('section', $sectionId);
    return self::get($cardId);
  }

  public static function draw($n, $location)
  {
    $cards = Cards::pickForLocation($n, 'deck', $location);
    if (!is_null($cards)) {
      foreach ($cards as $card) {
        $card->clearExtraDatas();
      }
    }

    return $cards;
  }

  public static function discard($cardId)
  {
    $cardId = is_int($cardId) ? $cardId : $cardId->getId();
    self::insertOnTop($cardId, 'discard');
  }

  public static function reshuffle()
  {
    self::reformDeckFromDiscard('deck');
    return self::countInLocation('deck');
  }

  public static function commissar($player, $cardId)
  {
    self::move($cardId, ['commissar', $player->getId()]);
    return self::getSingle($cardId);
  }

  public static function revealCommissar($player)
  {
    $pId = $player->getId();
    $cardId = self::getInLocation(['commissar', $pId])
      ->first()
      ->getId();
    self::move($cardId, ['inplay', $pId]);

    $last = Globals::getRawLastPlayedCards();
    !$last[$player->getId()] = self::DbQuery("SELECT * FROM `cards` WHERE `card_id` = $cardId");
    Globals::setRawLastPlayedCards($last);

    $last2 = Globals::getLastPlayedCards();
    $last2[$player->getId()] = $cardId;
    Globals::setLastPlayedCards($last2);

    return self::get($cardId);
  }

  /**
   * Load a scenario
   */
  public static function loadScenario($scenario)
  {
    self::DB()
      ->delete()
      ->run();
    $mode = $scenario['board']['type'];

    // Create deck
    $deckName = $scenario['game_info']['options']['deck_name'] ?? null;
    self::initDeck(self::$decks[$mode] ?? self::$decks[STANDARD_DECK], $deckName);
  }

  public static function initHands()
  {
    $scenario = Scenario::get();

    // Draw cards TODO
    if (true || Globals::getMode() == STANDARD_DECK) {
      foreach (Players::getAll() as $pId => $player) {
        $team = $player->getTeam();
        $cards = self::draw($team->getNCards(), ['hand', $pId]);
        Notifications::drawCards(Players::get($pId), $cards);
      }
    }
    // case scenario 5142 CounterAttack of the BEF : Air Power cannot be played by ALLIES
    $team = Teams::get(ALLIES);
    $player = $team -> getCommander();
    self::cannotPlayAirPower($player);
  }

  public static function cannotPlayAirPower($player) {
    // case scenario 5142 CounterAttack of the BEF : Air Power cannot be played by ALLIES
    $team = $player->getTeam();
    if(Scenario::getId() == 5142 && $team->getId() == ALLIES) {
      $cards = self::getOfPlayer($player-> getId());
      foreach($cards as $card) {
        if($card->getType() == CARD_AIR_POWER) {
          // if AIR_POWER card, discard it
          Cards::discard($card);
          Notifications::discardCard($player, $card); 
          // then draw a replacement card
          $newCards = Cards::draw(1, ['hand', $player->getId()]);
          Notifications::drawCards($player, $newCards);
        }
      }
    }
  }

  public static function initDeck($deck, $deckName)
  {
    $cards = [];
    foreach ($deck as $type => $occurences) {
      if ($type == \CARD_AIR_POWER && $deckName == 'AIR_POWER_AS_ARTILLERY_BOMBARD_DECK') {
        $type = \CARD_ARTILLERY_BOMBARD;
      }

      if (\is_array($occurences)) {
        foreach ($occurences as $value => $n) {
          $cards[] = [
            'type' => $type,
            'value' => $value,
            'nbr' => $n,
          ];
        }
      } else {
        $cards[] = [
          'type' => $type,
          'value' => 0,
          'nbr' => $occurences,
        ];
      }
    }

    self::DB()
      ->delete()
      ->run();
    self::create($cards, 'deck');
    self::shuffle('deck');
  }

  protected static $decks = [
    // OVERLORD is the same as STANDARD
    \STANDARD_DECK => [
      CARD_RECON => [2, 2, 2],
      CARD_PROBE => [4, 5, 4],
      CARD_ATTACK => [3, 4, 3],
      CARD_ASSAULT => [2, 2, 2],
      CARD_GENERAL_ADVANCE => 1,
      CARD_PINCER_MOVE => 1,
      CARD_RECON_IN_FORCE => 3,

      CARD_AIR_POWER => 1,
      CARD_AMBUSH => 1,
      CARD_ARMOR_ASSAULT => [1, 1], // TWO COPIES
      CARD_ARTILLERY_BOMBARD => 1,
      CARD_BARRAGE => 1,
      CARD_BEHIND_LINES => 1,
      CARD_CLOSE_ASSAULT => 1,
      CARD_COUNTER_ATTACK => [1, 1], // TWO COPIES
      CARD_DIG_IN => 1,
      CARD_DIRECT_FROM_HQ => [1, 1], // TWO COPIES
      CARD_FIREFIGHT => 1,
      CARD_INFANTRY_ASSAULT => [1, 1], // TWO COPIES
      CARD_MEDICS => 1,
      CARD_MOVE_OUT => [1, 1], // TWO COPIES
      CARD_FINEST_HOUR => 1,
    ],
    \BREAKTHROUGH_DECK => [
      CARD_RECON => [2, 2, 2],
      CARD_PROBE => [5, 6, 5],
      CARD_ATTACK => [4, 5, 4],
      CARD_ASSAULT => [3, 3, 3],
      CARD_GENERAL_ADVANCE => 2,
      CARD_PINCER_MOVE => 2,
      CARD_RECON_IN_FORCE => 4,

      CARD_AIR_POWER => 1,
      CARD_AMBUSH => 2,
      CARD_ARMOR_ASSAULT => [2, 1],
      CARD_ARTILLERY_BOMBARD => 2,
      CARD_BARRAGE => 1,
      CARD_BEHIND_LINES => 1,
      CARD_CLOSE_ASSAULT => 1,
      CARD_COUNTER_ATTACK => [2, 1],
      CARD_DIG_IN => 1,
      CARD_DIRECT_FROM_HQ => [2, 1],
      CARD_FIREFIGHT => 2,
      CARD_INFANTRY_ASSAULT => [1, 1],
      CARD_MEDICS => 1,
      CARD_MOVE_OUT => [2, 1],
      CARD_FINEST_HOUR => 2,
    ],
  ];
}
