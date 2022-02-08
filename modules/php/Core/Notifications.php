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
