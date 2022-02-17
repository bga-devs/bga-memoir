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

  public static function playCard($player, $card)
  {
    self::notifyAll('playCard', clienttranslate('${player_name} plays ${card_name}'), [
      'player' => $player,
      'card' => $card,
    ]);
  }

  // public static function orderUnits($player, $units, $unitsOnTheMove){

  public static function moveUnit($player, $unitId, $x, $y)
  {
    self::notifyAll('moveUnit', '', [
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
}

?>
