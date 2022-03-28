<?php
namespace M44\Managers;
use M44\Core\Globals;
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

  public function getInstance($type, $row = null)
  {
    $mode = Scenario::getMode();
    $dirs = [
      STANDARD_DECK => 'Standard',
      BREAKTHROUGH_DECK => 'Breakthrough',
      OVERLORD_DECK => 'Overlord',
    ];

    $className = '\M44\Cards\\' . $dirs[$mode] . '\\' . \CARD_CLASSES[$type];
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

  //////////////////////////////////
  //////////////////////////////////
  ///////////// SETTERS ////////////
  //////////////////////////////////
  //////////////////////////////////

  public static function play($player, $cardId, $sectionId)
  {
    self::move($cardId, ['inplay', $player->getId()]);
    $last = Globals::getLastPlayedCards();
    $last[$player->getId()] = $cardId;
    Globals::setLastPlayedCards($last);
    self::get($cardId)->setExtraDatas('section', $sectionId);
    return self::get($cardId);
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

  /**
   * Load a scenario
   */
  public function loadScenario($scenario)
  {
    self::DB()
      ->delete()
      ->run();
    $mode = $scenario['board']['type'];

    // Create deck
    $deckName = $scenario['game_info']['options']['deck_name'] ?? null;
    self::initDeck(self::$decks[$mode] ?? self::$decks[STANDARD_DECK], $deckName);
  }

  public function initHands()
  {
    $scenario = Scenario::get();

    // Draw cards TODO
    if (true || $mode == STANDARD_DECK) {
      foreach (Players::getAll() as $pId => $player) {
        $team = $player->getTeam();
        $cards = self::pickForLocation($team->getNCards(), 'deck', ['hand', $pId]);
        Notifications::drawCards(Players::get($pId), $cards);
      }
    }
  }

  public function initDeck($deck, $deckName)
  {
    $cards = [];
    foreach ($deck as $type => $occurences) {
      if ($type == \CARD_AIR_POWER && $deckName == "AIR_POWER_AS_ARTILLERY_BOMBARD_DECK") {
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
