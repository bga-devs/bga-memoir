<?php
namespace M44\Managers;
use M44\Core\Globals;
use M44\Core\Notifications;
use M44\Managers\Players;
use M44\Helpers\Utils;
use M44\Board;

/**
 * Cards: id, value, color
 *  pId is stored as second part of the location, eg : table_2322020
 */
class Cards extends \M44\Helpers\Pieces
{
  protected static $table = 'cards';
  protected static $prefix = 'card_';
  protected static $customFields = ['type', 'value', 'extra_datas'];
  protected static $autoreshuffle = false;
  protected static function cast($row)
  {
    $locations = explode('_', $row['location']);
    $row['player_id'] = $locations[1] ?? null;
    return self::getInstance($row['type'], $row);
  }

  public function getInstance($type, $row = null)
  {
    $mode = Board::getMode();
    $dirs = [
      \STANDARD_DECK => 'Standard',
      // TODO
    ];

    $className = '\M44\Cards\\' . $dirs[$mode] . '\\' . \CARD_CLASSES[$type];
    return new $className($row);
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

  //////////////////////////////////
  //////////////////////////////////
  ///////////// SETTERS //////////////
  //////////////////////////////////
  //////////////////////////////////

  /**
   * Load a scenario
   */
  public function loadScenario($scenario)
  {
    self::DB()
      ->delete()
      ->run();
    $mode = $scenario['board']['type'];

    if ($mode == STANDARD_DECK) {
      Cards::initStandardDeck();
      foreach (Players::getAll() as $pId => $player) {
        $team = $player->getTeam();
        self::pickForLocation($team['cards'], 'deck', ['hand', $pId]);
      }
    }
  }

  /**
   * Create a standard deck of cards
   */
  public function initStandardDeck()
  {
    $deck = [
      CARD_RECON => [2, 2, 2],
      CARD_PROBE => [4, 5, 4],
      CARD_ATTACK => [3, 4, 3],
      CARD_ASSAULT => [2, 2, 2],
      CARD_GENERAL_ADVANCE => 1,
      CARD_PINCER_MOVE => 1,
      CARD_RECON_IN_FORCE => 3,
    ];

    $cards = [];
    foreach ($deck as $type => $occurences) {
      if (\is_array($occurences)) {
        foreach ($occurences as $value => $n) {
          $cards[] = [
            'type' => $type,
            'value' => $value + 1,
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
}
