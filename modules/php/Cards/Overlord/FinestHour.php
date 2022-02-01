<?php
namespace M44\Cards\Overlord;

class FinestHour extends \M44\Cards\Standard\FinestHour
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->text = [
      clienttranslate('Each of your Field Generals roll 4d. For each unit symbol he rolls, he may order any 1 unit of that symbol type. For each star he rolls, he may order any 1 unit of any type.'),
      clienttranslate('All units he orders must be under his direct command. They battle with 1 additional die.'),
      clienttranslate('No other card may be played this turn. At the end of the turn, reshuffle the discard and draw piles together.'),
    ];
  }
}
