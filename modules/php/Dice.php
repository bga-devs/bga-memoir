<?php
namespace M44;
use M44\Core\Notifications;

class Dice
{
  /**
   * Roll dice : roll a given number of dices next to a given cell
   */
  public function roll($player, $nDice, $cell = null, $aggregate = true)
  {
    $dice = [\DICE_INFANTRY, \DICE_INFANTRY, \DICE_ARMOR, \DICE_FLAG, \DICE_STAR, \DICE_GRENADE];
    $results = [];
    for ($i = 0; $i < $nDice; $i++) {
      $k = array_rand($dice);
      $results[] = $dice[$k];
    }

    // debug
    // $results = [DICE_FLAG, \DICE_FLAG, DICE_GRENADE, \DICE_FLAG];

    // TODO keep track of stats to avoid player whining about life
    Notifications::rollDice($player, $nDice, $results, $cell);
    return $aggregate ? array_count_values($results) : $results;
  }
}
