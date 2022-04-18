<?php
namespace M44;
use M44\Core\Notifications;
use M44\Core\Stats;
use M44\Helpers\Log;

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
    // $results = [\DICE_STAR, \DICE_STAR,\DICE_STAR];
    // $results = [\DICE_FLAG, DICE_FLAG, DICE_FLAG];
    // $results = [\DICE_INFANTRY, DICE_INFANTRY, DICE_INFANTRY];

    Log::checkpoint(); // Make undo invalid
    Notifications::rollDice($player, $nDice, $results, $cell);

    $aggregated = array_count_values($results);

    // Increase corresponding stats
    Stats::incDiceCount($player, $nDice);
    $statNames = [
      \DICE_INFANTRY => 'DiceInf',
      \DICE_ARMOR => 'DiceArmor',
      \DICE_FLAG => 'DiceFlag',
      \DICE_STAR => 'DiceStar',
      \DICE_GRENADE => 'DiceGrenade',
    ];
    foreach ($aggregated as $face => $amount) {
      $statName = 'inc' . $statNames[$face];
      Stats::$statName($player, $amount);
    }

    return $aggregate ? $aggregated : $results;
  }
}
